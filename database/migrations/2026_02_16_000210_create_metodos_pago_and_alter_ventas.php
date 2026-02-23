<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetodosPagoAndAlterVentas extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('metodos_pago')) {
            Schema::create('metodos_pago', function (Blueprint $table) {
                $table->bigIncrements('id_metodo_pago');
                $table->string('nombre', 50)->unique();
                $table->string('estado', 20)->default('activo');
            });
        }
        if (Schema::hasTable('ventas') && !Schema::hasColumn('ventas','metodo_pago_id')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('metodo_pago_id')->nullable()->after('metodo_pago');
                $table->foreign('metodo_pago_id')->references('id_metodo_pago')->on('metodos_pago')->nullOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ventas') && Schema::hasColumn('ventas','metodo_pago_id')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->dropForeign(['metodo_pago_id']);
                $table->dropColumn('metodo_pago_id');
            });
        }
        if (Schema::hasTable('metodos_pago')) {
            Schema::dropIfExists('metodos_pago');
        }
    }
}
