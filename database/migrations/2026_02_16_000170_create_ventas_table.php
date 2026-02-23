<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ventas')) {
            Schema::create('ventas', function (Blueprint $table) {
                $table->bigIncrements('id_venta');
                $table->unsignedBigInteger('id_cliente');
                $table->unsignedBigInteger('id_usuario');
                $table->timestamp('fecha')->useCurrent();
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('descuento', 10, 2)->default(0);
                $table->decimal('impuesto', 10, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);
                $table->string('metodo_pago', 20)->default('efectivo');
                $table->string('estado', 20)->default('registrado');
                $table->timestamps();

                $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('restrict');
                $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('restrict');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ventas')) {
            Schema::dropIfExists('ventas');
        }
    }
}
