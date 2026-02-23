<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleComprasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('detalle_compras')) {
            Schema::create('detalle_compras', function (Blueprint $table) {
                $table->bigIncrements('id_detalle_compra');
                $table->unsignedBigInteger('id_compra');
                $table->unsignedBigInteger('id_producto');
                $table->integer('cantidad');
                $table->decimal('precio_compra', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->foreign('id_compra')->references('id_compra')->on('compras')->onDelete('cascade');
                $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('restrict');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('detalle_compras')) {
            Schema::dropIfExists('detalle_compras');
        }
    }
}
