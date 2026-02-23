<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('AddImagenToProductosTable')) {
    class AddImagenToProductosTable extends Migration
    {
        public function up()
        {
            // No-op: la columna 'imagen' ya existe en la migración de creación
        }

        public function down()
        {
            // No-op
        }
    }
}
