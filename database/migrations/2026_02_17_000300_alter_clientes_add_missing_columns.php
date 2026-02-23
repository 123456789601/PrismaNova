<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientesAddMissingColumns extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (!Schema::hasColumn('clientes', 'apellido')) {
                    $table->string('apellido', 100)->nullable()->after('nombre');
                }
                if (!Schema::hasColumn('clientes', 'documento')) {
                    $table->string('documento', 50)->nullable()->unique()->after('apellido');
                }
                if (!Schema::hasColumn('clientes', 'telefono')) {
                    $table->string('telefono', 50)->nullable()->after('documento');
                }
                if (!Schema::hasColumn('clientes', 'direccion')) {
                    $table->string('direccion', 191)->nullable()->after('telefono');
                }
                if (!Schema::hasColumn('clientes', 'email')) {
                    $table->string('email', 150)->nullable()->unique()->after('direccion');
                }
                if (!Schema::hasColumn('clientes', 'estado')) {
                    $table->string('estado', 20)->default('activo')->after('email');
                }
                if (!Schema::hasColumn('clientes', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (Schema::hasColumn('clientes', 'estado')) {
                    $table->dropColumn('estado');
                }
                if (Schema::hasColumn('clientes', 'email')) {
                    $table->dropUnique(['email']);
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('clientes', 'direccion')) {
                    $table->dropColumn('direccion');
                }
                if (Schema::hasColumn('clientes', 'telefono')) {
                    $table->dropColumn('telefono');
                }
                if (Schema::hasColumn('clientes', 'documento')) {
                    $table->dropUnique(['documento']);
                    $table->dropColumn('documento');
                }
                if (Schema::hasColumn('clientes', 'apellido')) {
                    $table->dropColumn('apellido');
                }
                // No removemos timestamps para evitar pérdida de metadatos si ya existen
            });
        }
    }
}
