<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosCajaTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('movimientos_caja')) {
            Schema::create('movimientos_caja', function (Blueprint $table) {
                $table->bigIncrements('id_movimiento');
                $table->unsignedBigInteger('id_caja');
                $table->string('tipo', 20); // ingreso | egreso
                $table->decimal('monto', 10, 2);
                $table->string('descripcion', 191)->nullable();
                $table->timestamp('fecha')->useCurrent();

                $table->foreign('id_caja')->references('id_caja')->on('caja')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('movimientos_caja')) {
            Schema::dropIfExists('movimientos_caja');
        }
    }
}
