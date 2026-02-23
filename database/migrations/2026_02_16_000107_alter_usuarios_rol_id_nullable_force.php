<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterUsuariosRolIdNullableForce extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios','rol_id')) {
            try {
                DB::statement("ALTER TABLE usuarios MODIFY rol_id BIGINT UNSIGNED NULL");
            } catch (\Throwable $e) {
                // ignore if not supported
            }
        }
    }

    public function down()
    {
        // no-op
    }
}
