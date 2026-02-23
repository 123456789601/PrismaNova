<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdjustUsernameOnUsuariosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios')) {
            if (Schema::hasColumn('usuarios', 'username')) {
                try {
                    DB::statement("ALTER TABLE usuarios DROP INDEX usuarios_username_unique");
                } catch (\Throwable $e) {
                    // ignore if index not present or named differently
                }
                try {
                    DB::statement("ALTER TABLE usuarios DROP INDEX username");
                } catch (\Throwable $e) {
                    // ignore if index not present
                }
                Schema::table('usuarios', function (Blueprint $table) {
                    $table->dropColumn('username');
                });
            }
            if (!Schema::hasColumn('usuarios', 'username')) {
                Schema::table('usuarios', function (Blueprint $table) {
                    $table->string('username', 100)->nullable()->unique()->after('email');
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios','username')) {
            try {
                DB::statement("ALTER TABLE usuarios DROP INDEX usuarios_username_unique");
            } catch (\Throwable $e) {
                // ignore
            }
            try {
                DB::statement("ALTER TABLE usuarios DROP INDEX username");
            } catch (\Throwable $e) {
                // ignore
            }
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }
    }
}
