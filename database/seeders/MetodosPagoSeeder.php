<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MetodosPagoSeeder extends Seeder
{
    public function run()
    {
        if (!Schema::hasTable('metodos_pago')) {
            return;
        }
        $base = ['Efectivo','Tarjeta','Transferencia'];
        foreach ($base as $nombre) {
            if (!DB::table('metodos_pago')->where('nombre',$nombre)->exists()) {
                DB::table('metodos_pago')->insert([
                    'nombre' => $nombre,
                    'estado' => 'activo',
                ]);
            }
        }
    }
}
