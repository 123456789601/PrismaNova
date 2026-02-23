<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterUsuariosRolEstadoMakeVarchar extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios')) {
            try {
                DB::statement("ALTER TABLE usuarios MODIFY rol VARCHAR(20) NOT NULL");
            } catch (\Throwable $e) {
                // ignore if already modified
            }
            try {
                DB::statement("ALTER TABLE usuarios MODIFY estado VARCHAR(20) NOT NULL");
            } catch (\Throwable $e) {
                // ignore if already modified
            }
        }
    }

    public function down()
    {
        // No-op reversible definition (evitar fallas en MySQL antiguo)
    }
}
