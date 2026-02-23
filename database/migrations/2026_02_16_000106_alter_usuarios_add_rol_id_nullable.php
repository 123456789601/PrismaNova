<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsuariosAddRolIdNullable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios','rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('rol_id')->nullable()->after('rol');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios','rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('rol_id');
            });
        }
    }
}
