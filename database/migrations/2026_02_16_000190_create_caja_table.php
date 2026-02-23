<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('caja')) {
            Schema::create('caja', function (Blueprint $table) {
                $table->bigIncrements('id_caja');
                $table->timestamp('fecha_apertura')->useCurrent();
                $table->decimal('monto_inicial', 10, 2)->default(0);
                $table->timestamp('fecha_cierre')->nullable();
                $table->decimal('monto_final', 10, 2)->nullable();
                $table->string('estado', 20)->default('abierta');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('caja')) {
            Schema::dropIfExists('caja');
        }
    }
}
