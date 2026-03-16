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
            $categorias = [
                'Bebidas',
                'Snacks',
                'Aseo',
                'Abarrotes',
                'Lácteos',
                'Carnes y embutidos',
                'Panadería',
                'Frutas y verduras',
                'Hogar',
                'Cuidado personal',
                'Mascotas',
                'Congelados',
            ];
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
        $ensureCategoryImage = function (string $categoryName): string {
            $slug = Str::slug($categoryName);
            $path = 'products/seed/'.$slug.'.svg';
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }

            $palette = [
                'bebidas' => ['#0ea5e9', '#0369a1'],
                'snacks' => ['#f97316', '#c2410c'],
                'aseo' => ['#22c55e', '#15803d'],
                'abarrotes' => ['#f59e0b', '#b45309'],
                'lacteos' => ['#60a5fa', '#1d4ed8'],
                'carnes-y-embutidos' => ['#ef4444', '#b91c1c'],
                'panaderia' => ['#a855f7', '#6d28d9'],
                'frutas-y-verduras' => ['#84cc16', '#3f6212'],
                'hogar' => ['#14b8a6', '#0f766e'],
                'cuidado-personal' => ['#ec4899', '#be185d'],
                'mascotas' => ['#8b5cf6', '#5b21b6'],
                'congelados' => ['#38bdf8', '#075985'],
            ];

            $colors = $palette[$slug] ?? ['#64748b', '#334155'];
            $title = htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8');

            $svg = '<?xml version="1.0" encoding="UTF-8"?>'
                .'<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600" role="img" aria-label="'.$title.'">'
                .'<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
                .'<stop offset="0" stop-color="'.$colors[0].'"/>'
                .'<stop offset="1" stop-color="'.$colors[1].'"/>'
                .'</linearGradient></defs>'
                .'<rect width="800" height="600" fill="url(#g)"/>'
                .'<rect x="40" y="40" width="720" height="520" rx="28" fill="rgba(255,255,255,0.14)"/>'
                .'<text x="400" y="320" text-anchor="middle" font-family="system-ui, -apple-system, Segoe UI, Roboto, Arial" font-size="44" font-weight="700" fill="white">'.$title.'</text>'
                .'<text x="400" y="380" text-anchor="middle" font-family="system-ui, -apple-system, Segoe UI, Roboto, Arial" font-size="20" fill="rgba(255,255,255,0.85)">PrismaNova</text>'
                .'</svg>';

            Storage::disk('public')->put($path, $svg);
            return $path;
        };

        // Crear productos
        if (Schema::hasTable('productos') && Schema::hasTable('categorias')) {
            $catIdCol = Schema::hasColumn('categorias','id_categoria') ? 'id_categoria' : (Schema::hasColumn('categorias','id') ? 'id' : null);
            $catIds = $catIdCol ? DB::table('categorias')->pluck($catIdCol,'nombre') : collect();
            $provIdCol = Schema::hasColumn('proveedores','id_proveedor') ? 'id_proveedor' : (Schema::hasColumn('proveedores','id') ? 'id' : null);
            $provId = ($provIdCol && Schema::hasTable('proveedores')) ? DB::table('proveedores')->value($provIdCol) : null;
            $productos = [
                ['nombre' => 'Agua Cristal 600ml', 'categoria' => 'Bebidas', 'precio_venta' => 2500],
                ['nombre' => 'Agua con gas 600ml', 'categoria' => 'Bebidas', 'precio_venta' => 2800],
                ['nombre' => 'Coca-Cola 400ml', 'categoria' => 'Bebidas', 'precio_venta' => 4200],
                ['nombre' => 'Coca-Cola 1.5L', 'categoria' => 'Bebidas', 'precio_venta' => 8900],
                ['nombre' => 'Postobón Manzana 400ml', 'categoria' => 'Bebidas', 'precio_venta' => 3500],
                ['nombre' => 'Jugo Hit Mora 500ml', 'categoria' => 'Bebidas', 'precio_venta' => 3800],
                ['nombre' => 'Jugo Del Valle 1L', 'categoria' => 'Bebidas', 'precio_venta' => 9800],
                ['nombre' => 'Cerveza Águila 330ml', 'categoria' => 'Bebidas', 'precio_venta' => 4500],
                ['nombre' => 'Cerveza Club Colombia 330ml', 'categoria' => 'Bebidas', 'precio_venta' => 5200],
                ['nombre' => 'Té frío 500ml', 'categoria' => 'Bebidas', 'precio_venta' => 3600],
                ['nombre' => 'Bebida energética 250ml', 'categoria' => 'Bebidas', 'precio_venta' => 6900],
                ['nombre' => 'Gatorade 500ml', 'categoria' => 'Bebidas', 'precio_venta' => 5200],

                ['nombre' => 'Papas Margarita Natural 105g', 'categoria' => 'Snacks', 'precio_venta' => 6500],
                ['nombre' => 'Doritos Nacho 95g', 'categoria' => 'Snacks', 'precio_venta' => 6200],
                ['nombre' => 'Choclitos 40g', 'categoria' => 'Snacks', 'precio_venta' => 2500],
                ['nombre' => 'Maní salado 80g', 'categoria' => 'Snacks', 'precio_venta' => 4200],
                ['nombre' => 'Galletas Festival Chocolate 12u', 'categoria' => 'Snacks', 'precio_venta' => 5200],
                ['nombre' => 'Galletas Saltín Noel 8u', 'categoria' => 'Snacks', 'precio_venta' => 4900],
                ['nombre' => 'Chocolatina Jet 12g', 'categoria' => 'Snacks', 'precio_venta' => 1000],
                ['nombre' => 'Bon Bon Bum 24g', 'categoria' => 'Snacks', 'precio_venta' => 900],
                ['nombre' => 'Chocorramo 65g', 'categoria' => 'Snacks', 'precio_venta' => 4200],
                ['nombre' => 'Gomitas 100g', 'categoria' => 'Snacks', 'precio_venta' => 3500],

                ['nombre' => 'Arroz Diana 1000g', 'categoria' => 'Abarrotes', 'precio_venta' => 6500],
                ['nombre' => 'Azúcar 1000g', 'categoria' => 'Abarrotes', 'precio_venta' => 5200],
                ['nombre' => 'Sal Refisal 500g', 'categoria' => 'Abarrotes', 'precio_venta' => 1800],
                ['nombre' => 'Harina Pan 1kg', 'categoria' => 'Abarrotes', 'precio_venta' => 8200],
                ['nombre' => 'Aceite Premier 1000ml', 'categoria' => 'Abarrotes', 'precio_venta' => 14900],
                ['nombre' => 'Atún Van Camps 160g', 'categoria' => 'Abarrotes', 'precio_venta' => 7200],
                ['nombre' => 'Pasta Doria Spaghetti 250g', 'categoria' => 'Abarrotes', 'precio_venta' => 2900],
                ['nombre' => 'Lenteja 500g', 'categoria' => 'Abarrotes', 'precio_venta' => 4200],
                ['nombre' => 'Fríjol cargamanto 500g', 'categoria' => 'Abarrotes', 'precio_venta' => 6800],
                ['nombre' => 'Salsa de tomate 400g', 'categoria' => 'Abarrotes', 'precio_venta' => 5200],
                ['nombre' => 'Mayonesa 400g', 'categoria' => 'Abarrotes', 'precio_venta' => 7900],
                ['nombre' => 'Café molido 250g', 'categoria' => 'Abarrotes', 'precio_venta' => 12500],
                ['nombre' => 'Chocolate de mesa 250g', 'categoria' => 'Abarrotes', 'precio_venta' => 9800],
                ['nombre' => 'Avena 400g', 'categoria' => 'Abarrotes', 'precio_venta' => 5900],
                ['nombre' => 'Cereal 300g', 'categoria' => 'Abarrotes', 'precio_venta' => 13900],

                ['nombre' => 'Leche Alquería 1L', 'categoria' => 'Lácteos', 'precio_venta' => 5200],
                ['nombre' => 'Leche deslactosada 1L', 'categoria' => 'Lácteos', 'precio_venta' => 5600],
                ['nombre' => 'Yogurt Alpina 1000g', 'categoria' => 'Lácteos', 'precio_venta' => 8900],
                ['nombre' => 'Queso campesino 500g', 'categoria' => 'Lácteos', 'precio_venta' => 15500],
                ['nombre' => 'Queso mozzarella 500g', 'categoria' => 'Lácteos', 'precio_venta' => 17900],
                ['nombre' => 'Mantequilla 250g', 'categoria' => 'Lácteos', 'precio_venta' => 12000],
                ['nombre' => 'Huevos AA x30', 'categoria' => 'Lácteos', 'precio_venta' => 21900],
                ['nombre' => 'Huevos AA x12', 'categoria' => 'Lácteos', 'precio_venta' => 9800],

                ['nombre' => 'Pechuga de pollo 1000g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 18900],
                ['nombre' => 'Carne molida 1000g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 24900],
                ['nombre' => 'Lomo de cerdo 1000g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 22900],
                ['nombre' => 'Salchicha Zenú 400g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 10900],
                ['nombre' => 'Jamón 250g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 11900],
                ['nombre' => 'Chorizo 500g', 'categoria' => 'Carnes y embutidos', 'precio_venta' => 13900],

                ['nombre' => 'Pan tajado Bimbo 500g', 'categoria' => 'Panadería', 'precio_venta' => 10500],
                ['nombre' => 'Arepas de maíz x5', 'categoria' => 'Panadería', 'precio_venta' => 6500],
                ['nombre' => 'Tortillas de trigo x8', 'categoria' => 'Panadería', 'precio_venta' => 12900],
                ['nombre' => 'Pan blandito x6', 'categoria' => 'Panadería', 'precio_venta' => 5200],

                ['nombre' => 'Banano 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 4200],
                ['nombre' => 'Manzana roja 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 8900],
                ['nombre' => 'Naranja 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 5200],
                ['nombre' => 'Tomate 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 5500],
                ['nombre' => 'Cebolla cabezona 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 4800],
                ['nombre' => 'Papa pastusa 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 4200],
                ['nombre' => 'Zanahoria 1000g', 'categoria' => 'Frutas y verduras', 'precio_venta' => 3500],
                ['nombre' => 'Lechuga', 'categoria' => 'Frutas y verduras', 'precio_venta' => 3200],
                ['nombre' => 'Aguacate', 'categoria' => 'Frutas y verduras', 'precio_venta' => 4900],

                ['nombre' => 'Detergente en polvo 1kg', 'categoria' => 'Aseo', 'precio_venta' => 18900],
                ['nombre' => 'Jabón Rey 300g', 'categoria' => 'Aseo', 'precio_venta' => 2800],
                ['nombre' => 'Lavaloza 750ml', 'categoria' => 'Aseo', 'precio_venta' => 8900],
                ['nombre' => 'Cloro 1000ml', 'categoria' => 'Aseo', 'precio_venta' => 3500],
                ['nombre' => 'Suavizante 1000ml', 'categoria' => 'Aseo', 'precio_venta' => 7500],
                ['nombre' => 'Papel higiénico x12', 'categoria' => 'Aseo', 'precio_venta' => 18900],
                ['nombre' => 'Toallas de cocina x2', 'categoria' => 'Aseo', 'precio_venta' => 6900],
                ['nombre' => 'Desinfectante 900ml', 'categoria' => 'Aseo', 'precio_venta' => 8200],

                ['nombre' => 'Bolsa de basura 20u', 'categoria' => 'Hogar', 'precio_venta' => 12900],
                ['nombre' => 'Bombillo LED 9W', 'categoria' => 'Hogar', 'precio_venta' => 6500],
                ['nombre' => 'Pilas AA x4', 'categoria' => 'Hogar', 'precio_venta' => 9800],
                ['nombre' => 'Esponjas x2', 'categoria' => 'Hogar', 'precio_venta' => 4200],
                ['nombre' => 'Servilletas x200', 'categoria' => 'Hogar', 'precio_venta' => 5200],

                ['nombre' => 'Shampoo 400ml', 'categoria' => 'Cuidado personal', 'precio_venta' => 15900],
                ['nombre' => 'Acondicionador 400ml', 'categoria' => 'Cuidado personal', 'precio_venta' => 15900],
                ['nombre' => 'Crema dental Colgate 75ml', 'categoria' => 'Cuidado personal', 'precio_venta' => 6500],
                ['nombre' => 'Cepillo dental', 'categoria' => 'Cuidado personal', 'precio_venta' => 3900],
                ['nombre' => 'Desodorante 150ml', 'categoria' => 'Cuidado personal', 'precio_venta' => 16900],
                ['nombre' => 'Jabón corporal x3', 'categoria' => 'Cuidado personal', 'precio_venta' => 9900],
                ['nombre' => 'Toallas higiénicas x10', 'categoria' => 'Cuidado personal', 'precio_venta' => 8900],

                ['nombre' => 'Concentrado perro 2kg', 'categoria' => 'Mascotas', 'precio_venta' => 24900],
                ['nombre' => 'Concentrado gato 1.5kg', 'categoria' => 'Mascotas', 'precio_venta' => 28900],
                ['nombre' => 'Arena para gato 5kg', 'categoria' => 'Mascotas', 'precio_venta' => 18900],
                ['nombre' => 'Snacks perro 100g', 'categoria' => 'Mascotas', 'precio_venta' => 7900],

                ['nombre' => 'Nuggets 500g', 'categoria' => 'Congelados', 'precio_venta' => 14900],
                ['nombre' => 'Papas a la francesa 1000g', 'categoria' => 'Congelados', 'precio_venta' => 12900],
                ['nombre' => 'Helado 1L', 'categoria' => 'Congelados', 'precio_venta' => 16900],
                ['nombre' => 'Verduras mixtas 500g', 'categoria' => 'Congelados', 'precio_venta' => 9900],
            ];
            foreach ($productos as $p) {
                if (!DB::table('productos')->where('nombre',$p['nombre'])->exists()) {
                    $img = $ensureCategoryImage($p['categoria']);
                    $row = [
                        'codigo_barras' => null,
                        'nombre' => $p['nombre'],
                        'descripcion' => null,
                        'imagen' => $img,
                        'id_categoria' => $catIds[$p['categoria']] ?? ($catIds->first() ?: null),
                        'id_proveedor' => $provId,
                        'precio_compra' => round(max($p['precio_venta'] * 0.78, 500), 2),
                        'precio_venta' => $p['precio_venta'],
                        'stock' => rand(12, 160),
                        'stock_minimo' => rand(3, 12),
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
