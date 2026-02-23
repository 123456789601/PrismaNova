<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('proveedores')) {
            Schema::create('proveedores', function (Blueprint $table) {
                $table->bigIncrements('id_proveedor');
                $table->string('nombre_empresa', 150);
                $table->string('nit', 50)->nullable()->unique();
                $table->string('contacto', 100)->nullable();
                $table->string('telefono', 50)->nullable();
                $table->string('direccion', 191)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('estado', 20)->default('activo');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('proveedores')) {
            Schema::dropIfExists('proveedores');
        }
    }
}
