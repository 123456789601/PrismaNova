<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->bigIncrements('id_producto');
                $table->string('codigo_barras', 50)->nullable()->unique();
                $table->string('nombre', 150);
                $table->string('descripcion', 191)->nullable();
                $table->string('imagen', 191)->nullable();
                $table->unsignedBigInteger('id_categoria');
                $table->unsignedBigInteger('id_proveedor')->nullable();
                $table->decimal('precio_compra', 10, 2)->default(0);
                $table->decimal('precio_venta', 10, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->integer('stock_minimo')->default(0);
                $table->date('fecha_vencimiento')->nullable();
                $table->string('estado', 20)->default('activo');
                $table->timestamps();

                $table->foreign('id_categoria')->references('id_categoria')->on('categorias')->onDelete('restrict');
                $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('productos')) {
            Schema::dropIfExists('productos');
        }
    }
}
