<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DropRolIdFromUsuarios extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios','rol_id')) {
            try {
                DB::statement('ALTER TABLE usuarios DROP FOREIGN KEY usuarios_ibfk_1');
            } catch (\Throwable $e) {
                // ignore if FK name differs or not present
            }
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('rol_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios','rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('rol_id')->nullable()->after('rol');
            });
        }
    }
}
