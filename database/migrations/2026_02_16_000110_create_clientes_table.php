<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('clientes')) {
            Schema::create('clientes', function (Blueprint $table) {
                $table->bigIncrements('id_cliente');
                $table->string('nombre', 100);
                $table->string('apellido', 100);
                $table->string('documento', 50)->unique();
                $table->string('telefono', 50)->nullable();
                $table->string('direccion', 191)->nullable();
                $table->string('email', 150)->unique();
                $table->string('estado', 20)->default('activo');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('clientes')) {
            Schema::dropIfExists('clientes');
        }
    }
}
