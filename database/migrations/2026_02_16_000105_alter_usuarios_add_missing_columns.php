<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsuariosAddMissingColumns extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios')) {
            Schema::table('usuarios', function (Blueprint $table) {
                if (!Schema::hasColumn('usuarios','documento')) {
                    $table->string('documento',50)->unique()->after('apellido');
                }
                if (!Schema::hasColumn('usuarios','rol')) {
                    $table->string('rol',20)->default('cliente')->after('password');
                }
                if (!Schema::hasColumn('usuarios','estado')) {
                    $table->string('estado',20)->default('activo')->after('rol');
                }
                if (!Schema::hasColumn('usuarios','remember_token')) {
                    $table->rememberToken()->nullable();
                }
                if (!Schema::hasColumn('usuarios','created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down()
    {
        // No reversible changes to avoid data loss
    }
}
