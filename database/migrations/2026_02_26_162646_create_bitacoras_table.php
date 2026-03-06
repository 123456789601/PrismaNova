<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitacorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id('id_bitacora');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('accion', 50); // LOGIN, CREATE, UPDATE, DELETE, EXPORT
            $table->string('tabla', 50)->nullable(); // usuarios, productos, ventas, etc.
            $table->unsignedBigInteger('registro_id')->nullable(); // ID del registro afectado
            $table->text('descripcion')->nullable(); // Detalles adicionales (JSON o texto)
            $table->string('ip', 45)->nullable();
            $table->string('navegador')->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitacoras');
    }
}
