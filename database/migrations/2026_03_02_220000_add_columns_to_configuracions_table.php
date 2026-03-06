<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToConfiguracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuracions', function (Blueprint $table) {
            $table->string('clave')->nullable()->unique()->after('id');
            $table->text('valor')->nullable()->after('clave');
            $table->string('descripcion')->nullable()->after('valor');
            $table->string('tipo')->default('text')->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuracions', function (Blueprint $table) {
            $table->dropColumn(['clave', 'valor', 'descripcion', 'tipo']);
        });
    }
}
