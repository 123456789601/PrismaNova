<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->bigIncrements('id_usuario');
                $table->string('nombre', 100);
                $table->string('apellido', 100);
                $table->string('documento', 50)->unique();
                $table->string('email', 150)->unique();
                $table->string('password', 191);
                $table->string('rol', 20)->default('cliente');
                $table->string('estado', 20)->default('activo');
                $table->rememberToken()->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('usuarios')) {
            Schema::dropIfExists('usuarios');
        }
    }
}
