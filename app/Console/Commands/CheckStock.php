<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Mail;

/**
 * Class CheckStock
 * 
 * Comando de consola para verificar el stock de productos.
 * Identifica productos con stock bajo y envía una notificación por correo electrónico.
 */
class CheckStock extends Command
{
    /**
     * Firma del comando para ejecución en consola.
     *
     * @var string
     */
    protected $signature = 'stock:check';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Verifica el stock de los productos y notifica si es bajo';

    /**
     * Crea una nueva instancia del comando.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ejecuta el comando de verificación de stock.
     * 
     * Busca productos cuyo stock actual sea menor o igual al stock mínimo
     * y envía una notificación por correo si se encuentran productos con stock bajo.
     *
     * @return int Código de salida (0 = éxito).
     */
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
