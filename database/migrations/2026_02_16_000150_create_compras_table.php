<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('compras')) {
            Schema::create('compras', function (Blueprint $table) {
                $table->bigIncrements('id_compra');
                $table->unsignedBigInteger('id_proveedor');
                $table->unsignedBigInteger('id_usuario');
                $table->timestamp('fecha')->useCurrent();
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('impuesto', 10, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);
                $table->string('estado', 20)->default('registrado');
                $table->timestamps();

                $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('restrict');
                $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('restrict');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('compras')) {
            Schema::dropIfExists('compras');
        }
    }
}
