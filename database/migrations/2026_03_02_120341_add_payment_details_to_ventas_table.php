<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDetailsToVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('monto_recibido', 10, 2)->nullable()->after('total');
            $table->decimal('cambio', 10, 2)->nullable()->after('monto_recibido');
            $table->string('referencia_pago', 50)->nullable()->after('cambio')->comment('Referencia de transacción para tarjetas');
            $table->string('ultimos_digitos', 4)->nullable()->after('referencia_pago')->comment('Últimos 4 dígitos de la tarjeta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['monto_recibido', 'cambio', 'referencia_pago', 'ultimos_digitos']);
        });
    }
}
