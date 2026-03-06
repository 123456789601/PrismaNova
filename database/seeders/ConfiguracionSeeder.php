<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configs = [
            [
                'clave' => 'nombre_tienda',
                'valor' => 'PrismaNova Store',
                'descripcion' => 'Nombre de tu negocio que aparecerá en el sistema y tickets',
                'tipo' => 'text'
            ],
            [
                'clave' => 'moneda',
                'valor' => '$',
                'descripcion' => 'Símbolo de moneda para precios (ej: $ para COP)',
                'tipo' => 'text'
            ],
            [
                'clave' => 'impuesto',
                'valor' => '19',
                'descripcion' => 'Porcentaje de IVA aplicado a las ventas (%)',
                'tipo' => 'number'
            ],
            [
                'clave' => 'direccion_tienda',
                'valor' => 'Av. Principal 123, Bogotá',
                'descripcion' => 'Dirección del local para los comprobantes',
                'tipo' => 'text'
            ],
            [
                'clave' => 'telefono_contacto',
                'valor' => '300 123 4567',
                'descripcion' => 'Número de contacto para atención al cliente',
                'tipo' => 'text'
            ],
            [
                'clave' => 'email_contacto',
                'valor' => 'contacto@prismanova.com',
                'descripcion' => 'Correo electrónico oficial de la tienda',
                'tipo' => 'email'
            ],
             [
                'clave' => 'mensaje_ticket',
                'valor' => '¡Gracias por su compra!',
                'descripcion' => 'Mensaje de despedida al final del ticket',
                'tipo' => 'text'
            ],
            [
                'clave' => 'whatsapp_soporte',
                'valor' => '573108458637',
                'descripcion' => 'Número de WhatsApp para soporte técnico (sin el +)',
                'tipo' => 'text'
            ],
            [
                'clave' => 'email_soporte',
                'valor' => 'alejandroaris12300@gmail.com',
                'descripcion' => 'Correo electrónico para soporte técnico',
                'tipo' => 'email'
            ],
            [
                'clave' => 'horario_atencion',
                'valor' => 'Lunes a Viernes: 8:00 AM - 6:00 PM',
                'descripcion' => 'Horario de atención al cliente mostrado en contacto',
                'tipo' => 'text'
            ],
        ];

        foreach ($configs as $config) {
            Configuracion::updateOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}
