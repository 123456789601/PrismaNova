<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table) {
                $table->bigIncrements('id_categoria');
                $table->string('nombre', 120)->unique();
                $table->string('descripcion', 191)->nullable();
                $table->string('estado', 20)->default('activo');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('categorias')) {
            Schema::dropIfExists('categorias');
        }
    }
}
