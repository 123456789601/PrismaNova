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
        // Ensure roles exist
        $adminRol = DB::table('roles')->where('nombre', 'admin')->first();
        if (!$adminRol) {
            $id = DB::table('roles')->insertGetId(['nombre' => 'admin', 'descripcion' => 'Administrador del sistema', 'created_at' => now(), 'updated_at' => now()]);
            $adminRol = (object)['id' => $id];
        }

        $cajeroRol = DB::table('roles')->where('nombre', 'cajero')->first();
        if (!$cajeroRol) {
            $id = DB::table('roles')->insertGetId(['nombre' => 'cajero', 'descripcion' => 'Cajero del sistema', 'created_at' => now(), 'updated_at' => now()]);
            $cajeroRol = (object)['id' => $id];
        }

        $clienteRol = DB::table('roles')->where('nombre', 'cliente')->first();
        if (!$clienteRol) {
            $id = DB::table('roles')->insertGetId(['nombre' => 'cliente', 'descripcion' => 'Cliente del sistema', 'created_at' => now(), 'updated_at' => now()]);
            $clienteRol = (object)['id' => $id];
        }
        
        // Also ensure bodeguero role exists
        $bodegueroRol = DB::table('roles')->where('nombre', 'bodeguero')->first();
        if (!$bodegueroRol) {
            $id = DB::table('roles')->insertGetId(['nombre' => 'bodeguero', 'descripcion' => 'Bodeguero del sistema', 'created_at' => now(), 'updated_at' => now()]);
            $bodegueroRol = (object)['id' => $id];
        }

        if (!DB::table('usuarios')->where('email','admin@prismanova.local')->exists()) {
            $data = [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@prismanova.local',
                'password' => Hash::make('admin123'),
                'rol_id' => $adminRol->id,
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
            DB::table('usuarios')->insert($data);
        }
        if (!DB::table('usuarios')->where('email','cajero@prismanova.local')->exists()) {
            $data = [
                'nombre' => 'Cajero',
                'apellido' => 'Demo',
                'email' => 'cajero@prismanova.local',
                'password' => Hash::make('cajero123'),
                'rol_id' => $cajeroRol->id,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('usuarios','documento')) {
                $data['documento'] = 'CAJERO-0001';
            }
            if (Schema::hasColumn('usuarios','username')) {
                $data['username'] = 'cajero';
            }
            DB::table('usuarios')->insert($data);
        }
        if (!DB::table('usuarios')->where('email','cliente@prismanova.local')->exists()) {
            $data = [
                'nombre' => 'Cliente',
                'apellido' => 'Demo',
                'email' => 'cliente@prismanova.local',
                'password' => Hash::make('cliente123'),
                'rol_id' => $clienteRol->id,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('usuarios','documento')) {
                $data['documento'] = 'CLIENTE-0001';
            }
            if (Schema::hasColumn('usuarios','username')) {
                $data['username'] = 'cliente';
            }
            DB::table('usuarios')->insert($data);
        }
    }
}
