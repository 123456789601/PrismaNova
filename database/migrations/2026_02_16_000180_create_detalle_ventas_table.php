<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleVentasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('detalle_ventas')) {
            Schema::create('detalle_ventas', function (Blueprint $table) {
                $table->bigIncrements('id_detalle');
                $table->unsignedBigInteger('id_venta');
                $table->unsignedBigInteger('id_producto');
                $table->integer('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('cascade');
                $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('restrict');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('detalle_ventas')) {
            Schema::dropIfExists('detalle_ventas');
        }
    }
}
