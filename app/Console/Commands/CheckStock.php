<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Mail;

class CheckStock extends Command
{
    protected $signature = 'stock:check';
    protected $description = 'Verifica el stock de los productos y notifica si es bajo';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $lowStockProducts = Producto::whereColumn('stock', '<=', 'stock_minimo')->get();

        if ($lowStockProducts->count() > 0) {
            $this->info("Encontrados {$lowStockProducts->count()} productos con stock bajo.");

            $messageContent = "Los siguientes productos tienen stock bajo:\n\n";
            foreach ($lowStockProducts as $product) {
                $messageContent .= "- {$product->nombre}: Stock {$product->stock} (Mínimo: {$product->stock_minimo})\n";
            }

            try {
                Mail::raw($messageContent, function ($message) {
                    $message->to('alejandroaris12300@gmail.com')
                            ->subject('Alerta de Stock Bajo - PrismaNova');
                });
                $this->info('Notificación enviada correctamente.');
            } catch (\Exception $e) {
                $this->error('Error al enviar correo: ' . $e->getMessage());
            }
        } else {
            $this->info('Todos los productos tienen stock suficiente.');
        }

        return 0;
    }
}
