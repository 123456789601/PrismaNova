<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('cupones')) {
            Schema::create('cupones', function (Blueprint $table) {
                $table->increments('id_cupon');
                $table->string('codigo', 50)->unique();
                $table->enum('tipo', ['fijo','porcentaje'])->default('porcentaje');
                $table->decimal('valor', 10, 2)->default(0);
                $table->dateTime('fecha_inicio')->nullable();
                $table->dateTime('fecha_fin')->nullable();
                $table->enum('estado', ['activo','inactivo'])->default('activo');
                $table->integer('uso_maximo')->nullable();
                $table->integer('usos')->default(0);
                $table->timestamps();
            });
            DB::table('cupones')->insert([
                [
                    'codigo' => 'PROMO10',
                    'tipo' => 'porcentaje',
                    'valor' => 10,
                    'fecha_inicio' => now()->subDay(),
                    'fecha_fin' => now()->addMonths(6),
                    'estado' => 'activo',
                    'uso_maximo' => null,
                    'usos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'codigo' => 'DESC50',
                    'tipo' => 'fijo',
                    'valor' => 50,
                    'fecha_inicio' => now()->subDay(),
                    'fecha_fin' => now()->addMonths(6),
                    'estado' => 'activo',
                    'uso_maximo' => 100,
                    'usos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cupones')) {
            Schema::dropIfExists('cupones');
        }
    }
};
