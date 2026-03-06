<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransaccionesDemoSeeder extends Seeder
{
    public function run()
    {
        // Helpers
        $insertFiltered = function (string $table, array $data) {
            $cols = Schema::getColumnListing($table);
            $filtered = array_intersect_key($data, array_flip($cols));
            return DB::table($table)->insertGetId($filtered, $this->pkName($table));
        };

        // Obtener IDs necesarios
        $rolAdminId = DB::table('roles')->where('nombre', 'admin')->value('id');
        $adminRow = DB::table('usuarios')->where('rol_id', $rolAdminId)->first();
        $adminId = $adminRow->id_usuario ?? ($adminRow->id ?? null);
        $cliente = DB::table('clientes')->where('email','cliente@prismanova.local')->first();
        if (!$adminId || !$cliente) {
            return;
        }
        $clienteId = $cliente->id_cliente ?? ($cliente->id ?? null);

        // Caja: abrir si no existe
        $cajaId = DB::table('caja')->where('estado','abierta')->value('id_caja');
        if (!$cajaId) {
            $cajaId = $insertFiltered('caja', [
                'fecha_apertura' => now(),
                'monto_inicial' => 100.00,
                'estado' => 'abierta',
            ]);
        }

        // Elegir 2 productos existentes
        $productos = DB::table('productos')->limit(2)->get();
        if ($productos->count() < 2) {
            return;
        }
        $p1 = $productos[0];
        $p2 = $productos[1];

        // Registrar COMPRA (aumenta stock)
        $idProd1 = $p1->id_producto ?? ($p1->id ?? null);
        $idProd2 = $p2->id_producto ?? ($p2->id ?? null);
        $precioCompra1 = isset($p1->precio_compra) ? $p1->precio_compra : (isset($p1->precio_venta) ? max($p1->precio_venta - 0.8, 1) : 1.0);
        $precioCompra2 = isset($p2->precio_compra) ? $p2->precio_compra : (isset($p2->precio_venta) ? max($p2->precio_venta - 0.8, 1) : 1.0);

        $compraDetalles = [
            ['id_producto' => $idProd1, 'cantidad' => 20, 'precio' => $precioCompra1],
            ['id_producto' => $idProd2, 'cantidad' => 15, 'precio' => $precioCompra2],
        ];
        $subtotalC = 0;
        foreach ($compraDetalles as $d) {
            $subtotalC += $d['cantidad'] * $d['precio'];
        }
        $impuestoC = round($subtotalC * 0.0, 2);
        $totalC = $subtotalC + $impuestoC;

        $provPk = Schema::hasColumn('proveedores','id_proveedor') ? 'id_proveedor' : 'id';
        $proveedorId = DB::table('proveedores')->value($provPk);
        if (!$proveedorId) {
            $proveedorId = DB::table('proveedores')->insertGetId(
                ['nombre_empresa'=>'Proveedor Genérico','created_at'=>now(),'updated_at'=>now()],
                $provPk
            );
        }
        $compraData = [
            'fecha' => now(),
            'subtotal' => $subtotalC,
            'impuesto' => $impuestoC,
            'total' => $totalC,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        // map FKs dynamically
        if (Schema::hasColumn('compras','id_proveedor')) $compraData['id_proveedor'] = $proveedorId;
        elseif (Schema::hasColumn('compras','proveedor_id')) $compraData['proveedor_id'] = $proveedorId;
        if (Schema::hasColumn('compras','id_usuario')) $compraData['id_usuario'] = $adminId;
        elseif (Schema::hasColumn('compras','usuario_id')) $compraData['usuario_id'] = $adminId;
        if (Schema::hasColumn('compras','folio')) $compraData['folio'] = 'CMP-'.now()->format('YmdHis');
        $compraId = $insertFiltered('compras', $compraData);
        foreach ($compraDetalles as $d) {
            $dc = [
                'cantidad' => $d['cantidad'],
                'subtotal' => $d['cantidad'] * $d['precio'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('detalle_compras','precio_compra')) $dc['precio_compra'] = $d['precio'];
            elseif (Schema::hasColumn('detalle_compras','precio_unitario')) $dc['precio_unitario'] = $d['precio'];
            if (Schema::hasColumn('detalle_compras','id_compra')) $dc['id_compra'] = $compraId;
            elseif (Schema::hasColumn('detalle_compras','compra_id')) $dc['compra_id'] = $compraId;
            if (Schema::hasColumn('detalle_compras','id_producto')) $dc['id_producto'] = $d['id_producto'];
            elseif (Schema::hasColumn('detalle_compras','producto_id')) $dc['producto_id'] = $d['id_producto'];
            $insertFiltered('detalle_compras', $dc);
            if (Schema::hasColumn('productos','stock')) {
                $pk = Schema::hasColumn('productos','id_producto') ? 'id_producto' : 'id';
                DB::table('productos')->where($pk,$d['id_producto'])->increment('stock', $d['cantidad']);
            }
        }

        // Registrar VENTA (disminuye stock)
        $precioVenta1 = isset($p1->precio_venta) ? $p1->precio_venta : ($precioCompra1 + 1);
        $precioVenta2 = isset($p2->precio_venta) ? $p2->precio_venta : ($precioCompra2 + 1);
        $ventaDetalles = [
            ['id_producto' => $idProd1, 'cantidad' => 3, 'precio' => $precioVenta1],
            ['id_producto' => $idProd2, 'cantidad' => 2, 'precio' => $precioVenta2],
        ];
        $subtotalV = 0;
        foreach ($ventaDetalles as $d) {
            $subtotalV += $d['cantidad'] * $d['precio'];
        }
        $descuentoV = 0.00;
        $impuestoV = round($subtotalV * 0.0, 2);
        $totalV = $subtotalV - $descuentoV + $impuestoV;

        $ventaData = [
            'fecha' => now(),
            'subtotal' => $subtotalV,
            'descuento' => $descuentoV,
            'impuesto' => $impuestoV,
            'total' => $totalV,
            'metodo_pago' => 'efectivo',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('ventas','id_cliente')) $ventaData['id_cliente'] = $clienteId;
        elseif (Schema::hasColumn('ventas','cliente_id')) $ventaData['cliente_id'] = $clienteId;
        if (Schema::hasColumn('ventas','id_usuario')) $ventaData['id_usuario'] = $adminId;
        elseif (Schema::hasColumn('ventas','usuario_id')) $ventaData['usuario_id'] = $adminId;
        // Soporte a esquemas con metodo_pago_id
        if (Schema::hasColumn('ventas','metodo_pago_id') && Schema::hasTable('metodos_pago')) {
            $cols = Schema::getColumnListing('metodos_pago');
            $mpPk = in_array('id_metodo_pago',$cols) ? 'id_metodo_pago' : (in_array('id',$cols) ? 'id' : null);
            $mpId = $mpPk ? DB::table('metodos_pago')->value($mpPk) : null;
            if (!$mpId) {
                $row = [];
                if (in_array('nombre',$cols)) $row['nombre'] = 'Efectivo';
                if (in_array('estado',$cols)) $row['estado'] = 'activo';
                $mpId = DB::table('metodos_pago')->insertGetId($row, $mpPk ?? 'id');
            }
            $ventaData['metodo_pago_id'] = $mpId;
        }
        if (Schema::hasColumn('ventas','folio')) $ventaData['folio'] = 'VEN-'.now()->format('YmdHis');
        if (Schema::hasColumn('ventas','numero')) $ventaData['numero'] = now()->format('YmdHis');
        $ventaId = $insertFiltered('ventas', $ventaData);
        foreach ($ventaDetalles as $d) {
            $dv = [
                'cantidad' => $d['cantidad'],
                'precio_unitario' => $d['precio'],
                'subtotal' => $d['cantidad'] * $d['precio'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('detalle_ventas','id_venta')) $dv['id_venta'] = $ventaId;
            elseif (Schema::hasColumn('detalle_ventas','venta_id')) $dv['venta_id'] = $ventaId;
            if (Schema::hasColumn('detalle_ventas','id_producto')) $dv['id_producto'] = $d['id_producto'];
            elseif (Schema::hasColumn('detalle_ventas','producto_id')) $dv['producto_id'] = $d['id_producto'];
            $insertFiltered('detalle_ventas', $dv);
            if (Schema::hasColumn('productos','stock')) {
                $pk = Schema::hasColumn('productos','id_producto') ? 'id_producto' : 'id';
                DB::table('productos')->where($pk,$d['id_producto'])->decrement('stock', $d['cantidad']);
            }
        }

        // Movimiento de caja por venta
        $insertFiltered('movimientos_caja', [
            'id_caja' => $cajaId,
            'tipo' => 'ingreso',
            'monto' => $totalV,
            'descripcion' => 'Venta demo #'.$ventaId,
            'fecha' => now(),
        ]);
    }

    private function pkName(string $table): string
    {
        // Mapea algunas PKs conocidas
        return [
            'usuarios' => 'id_usuario',
            'clientes' => 'id_cliente',
            'proveedores' => 'id_proveedor',
            'categorias' => 'id_categoria',
            'productos' => 'id_producto',
            'compras' => 'id_compra',
            'detalle_compras' => 'id_detalle_compra',
            'ventas' => 'id_venta',
            'detalle_ventas' => 'id_detalle',
            'caja' => 'id_caja',
            'movimientos_caja' => 'id_movimiento',
        ][$table] ?? 'id';
    }
}
