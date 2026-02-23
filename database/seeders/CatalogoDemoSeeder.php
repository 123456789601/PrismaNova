<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogoDemoSeeder extends Seeder
{
    public function run()
    {
        // Helper para insertar solo columnas existentes
        $insertFiltered = function (string $table, array $data) {
            $cols = Schema::getColumnListing($table);
            $filtered = array_intersect_key($data, array_flip($cols));
            return DB::table($table)->insert($filtered);
        };

        // Crear categorías
        if (Schema::hasTable('categorias')) {
            $categorias = ['Bebidas','Snacks','Aseo'];
            foreach ($categorias as $nombre) {
                if (!DB::table('categorias')->where('nombre',$nombre)->exists()) {
                    $insertFiltered('categorias', [
                        'nombre' => $nombre,
                        'descripcion' => null,
                        'estado' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Crear proveedores
        if (Schema::hasTable('proveedores')) {
            $provNameCol = null;
            if (Schema::hasColumn('proveedores','nombre_empresa')) $provNameCol = 'nombre_empresa';
            elseif (Schema::hasColumn('proveedores','nombre')) $provNameCol = 'nombre';
            
            if ($provNameCol) {
            $proveedores = [
                ['nombre_empresa' => 'Proveedor Uno', 'nombre' => 'Proveedor Uno', 'nit' => '1001'],
                ['nombre_empresa' => 'Proveedor Dos', 'nombre' => 'Proveedor Dos', 'nit' => '1002'],
            ];
            foreach ($proveedores as $p) {
                $nameVal = $p[$provNameCol];
                if (!DB::table('proveedores')->where($provNameCol,$nameVal)->exists()) {
                    $insertFiltered('proveedores', array_merge($p, [
                        'contacto' => null,
                        'telefono' => null,
                        'direccion' => null,
                        'email' => null,
                        'estado' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
            }
        }

        // Helper: crear PNG 1x1 y devolver ruta
        $ensureImage = function (string $filename): string {
            $path = 'products/'.$filename;
            if (!Storage::disk('public')->exists($path)) {
                $base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
                Storage::disk('public')->put($path, base64_decode($base64));
            }
            return $path;
        };

        // Crear productos
        if (Schema::hasTable('productos') && Schema::hasTable('categorias')) {
            $catIdCol = Schema::hasColumn('categorias','id_categoria') ? 'id_categoria' : (Schema::hasColumn('categorias','id') ? 'id' : null);
            $catIds = $catIdCol ? DB::table('categorias')->pluck($catIdCol,'nombre') : collect();
            $provIdCol = Schema::hasColumn('proveedores','id_proveedor') ? 'id_proveedor' : (Schema::hasColumn('proveedores','id') ? 'id' : null);
            $provId = ($provIdCol && Schema::hasTable('proveedores')) ? DB::table('proveedores')->value($provIdCol) : null;
            $productos = [
                ['nombre' => 'Agua Mineral 500ml', 'categoria' => 'Bebidas', 'precio_venta' => 2.50],
                ['nombre' => 'Gaseosa Cola 350ml', 'categoria' => 'Bebidas', 'precio_venta' => 3.00],
                ['nombre' => 'Papas Fritas 45g', 'categoria' => 'Snacks', 'precio_venta' => 2.00],
                ['nombre' => 'Galletas Chocolate 100g', 'categoria' => 'Snacks', 'precio_venta' => 2.20],
                ['nombre' => 'Detergente 500g', 'categoria' => 'Aseo', 'precio_venta' => 4.50],
                ['nombre' => 'Jabón de manos 250ml', 'categoria' => 'Aseo', 'precio_venta' => 3.80],
            ];
            foreach ($productos as $p) {
                if (!DB::table('productos')->where('nombre',$p['nombre'])->exists()) {
                    $filename = Str::slug($p['nombre']).'.png';
                    $img = $ensureImage($filename);
                    $row = [
                        'codigo_barras' => strtoupper(Str::random(12)),
                        'nombre' => $p['nombre'],
                        'descripcion' => null,
                        'imagen' => $img,
                        'id_categoria' => $catIds[$p['categoria']] ?? ($catIds->first() ?: null),
                        'id_proveedor' => $provId,
                        'precio_compra' => max($p['precio_venta'] - 0.8, 1.00),
                        'precio_venta' => $p['precio_venta'],
                        'stock' => 50,
                        'stock_minimo' => 5,
                        'fecha_vencimiento' => null,
                        'estado' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $cols = Schema::getColumnListing('productos');
                    if (in_array('codigo', $cols) && !isset($row['codigo'])) {
                        $row['codigo'] = strtoupper(Str::random(10));
                    }
                    if (in_array('categoria_id', $cols) && !isset($row['categoria_id'])) {
                        $row['categoria_id'] = $row['id_categoria'] ?? null;
                    }
                    if (in_array('proveedor_id', $cols) && !isset($row['proveedor_id'])) {
                        $row['proveedor_id'] = $row['id_proveedor'] ?? null;
                    }
                    $insertFiltered('productos', $row);
                }
            }
        }

        // Crear usuario cliente demo y su ficha de cliente
        if (Schema::hasTable('usuarios')) {
            if (!DB::table('usuarios')->where('email','cliente@prismanova.local')->exists()) {
                $userData = [
                    'nombre' => 'Cliente',
                    'apellido' => 'Demo',
                    'documento' => 'CLI-0001',
                    'email' => 'cliente@prismanova.local',
                    'password' => Hash::make('cliente123'),
                    'rol' => 'cliente',
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('usuarios','username')) {
                    $userData['username'] = 'cliente';
                }
                $usuarioId = DB::table('usuarios')->insertGetId($userData, 'id_usuario');
            }
        }
        if (Schema::hasTable('clientes')) {
            if (!DB::table('clientes')->where('email','cliente@prismanova.local')->exists()) {
                $insertFiltered('clientes', [
                    'nombre' => 'Cliente',
                    'apellido' => 'Demo',
                    'documento' => 'CLI-0001',
                    'telefono' => null,
                    'direccion' => null,
                    'email' => 'cliente@prismanova.local',
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
