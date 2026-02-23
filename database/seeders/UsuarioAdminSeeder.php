<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsuarioAdminSeeder extends Seeder
{
    public function run()
    {
        if (!DB::table('usuarios')->where('email','admin@prismanova.local')->exists()) {
            $data = [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@prismanova.local',
                'password' => Hash::make('admin123'),
                'rol' => 'admin',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('usuarios','documento')) {
                $data['documento'] = 'ADMIN-0001';
            }
            if (Schema::hasColumn('usuarios','username')) {
                $data['username'] = 'admin';
            }
            // No usamos 'rol_id'; el sistema funciona con la columna 'rol'
            DB::table('usuarios')->insert($data);
        }
    }
}
