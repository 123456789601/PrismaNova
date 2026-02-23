<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\InventoryUsageSync;

class SyncInventoryUsage extends Command
{
    protected $signature = 'inventory:sync-usage {--url=} {--token=}';
    protected $description = 'Sincroniza consumos de productos desde API externa y actualiza stock';

    public function handle(): int
    {
        $url = $this->option('url') ?: env('INVENTORY_USAGE_API_URL');
        $token = $this->option('token') ?: env('INVENTORY_USAGE_API_TOKEN');
        if (!$url) {
            $this->warn('INVENTORY_USAGE_API_URL no configurado. Saliendo.');
            return self::SUCCESS;
        }
        $req = Http::timeout(20);
        if ($token) $req = $req->withToken($token);
        try {
            $res = $req->get($url);
        } catch (\Throwable $e) {
            $this->error('Error HTTP: '.$e->getMessage());
            return self::FAILURE;
        }
        if (!$res->ok()) {
            $this->error('Respuesta no OK: '.$res->status());
            return self::FAILURE;
        }
        $items = $res->json();
        if (!is_array($items)) {
            $this->warn('Formato inesperado');
            return self::SUCCESS;
        }
        $procesados = 0;
        foreach ($items as $item) {
            $externalId = $item['id'] ?? null;
            $cant = (int)($item['cantidad'] ?? 0);
            if (!$externalId || $cant <= 0) continue;
            if (InventoryUsageSync::where('external_id',$externalId)->exists()) {
                continue;
            }
            DB::beginTransaction();
            try {
                $producto = null;
                if (isset($item['id_producto'])) {
                    $producto = Producto::lockForUpdate()->find($item['id_producto']);
                } elseif (isset($item['codigo_barras'])) {
                    $producto = Producto::lockForUpdate()->where('codigo_barras',$item['codigo_barras'])->first();
                }
                if (!$producto) {
                    DB::rollBack();
                    continue;
                }
                $nuevo = max(0, $producto->stock - $cant);
                $producto->update(['stock' => $nuevo]);
                InventoryUsageSync::create([
                    'external_id' => $externalId,
                    'payload' => $item,
                    'applied_at' => now(),
                ]);
                DB::commit();
                $procesados++;
            } catch (\Throwable $e) {
                DB::rollBack();
                continue;
            }
        }
        $this->info("Consumos procesados: $procesados");
        return self::SUCCESS;
    }
}
