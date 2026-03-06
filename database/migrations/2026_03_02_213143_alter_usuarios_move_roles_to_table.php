<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterUsuariosMoveRolesToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix for table name if created as 'rols' instead of 'roles'
        if (Schema::hasTable('rols') && !Schema::hasTable('roles')) {
            Schema::rename('rols', 'roles');
        }

        // Fix engine issue (MyISAM vs InnoDB)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE roles ENGINE = InnoDB');
        }

        // Fix for partial migration failure: drop rol_id if exists
        if (Schema::hasColumn('usuarios', 'rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                // Try to drop foreign key first just in case, wrapped in try/catch if needed, 
                // but schema builder doesn't throw easily on dropForeign unless it doesn't exist? 
                // Actually, let's just drop column, it should drop FK in some DBs, but explicitly dropping FK is better.
                // Since we don't know if FK exists, we can skip dropping FK or try it.
                // Let's just drop column. MySQL usually complains if FK exists.
                // But since FK creation failed, it shouldn't exist.
                $table->dropColumn('rol_id');
            });
        }

        // 1. Add rol_id column
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('rol_id')->nullable()->after('rol');
            // Assuming roles table exists (created in previous migration)
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('set null');
        });

        // 2. Get distinct roles from usuarios and populate roles table
        $roles = DB::table('usuarios')->select('rol')->distinct()->whereNotNull('rol')->pluck('rol');

        foreach ($roles as $roleName) {
            // Trim whitespace
            $roleName = trim($roleName);
            if (empty($roleName)) continue;

            $exists = DB::table('roles')->where('nombre', $roleName)->exists();
            if (!$exists) {
                DB::table('roles')->insert([
                    'nombre' => $roleName,
                    'descripcion' => 'Rol generado automáticamente desde usuarios',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Update usuarios.rol_id for this role
            $roleId = DB::table('roles')->where('nombre', $roleName)->value('id');
            DB::table('usuarios')->where('rol', $roleName)->update(['rol_id' => $roleId]);
        }

        // 3. Update usuarios.rol_id based on rol name (Removed: Done in loop above for compatibility)
        // DB::statement("UPDATE usuarios u JOIN roles r ON u.rol = r.nombre SET u.rol_id = r.id");

        // 4. Drop old rol column
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Add rol column back
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('rol')->nullable()->after('password');
        });

        // 2. Restore rol names from roles table
        $roles = DB::table('roles')->get();
        foreach ($roles as $rol) {
            DB::table('usuarios')->where('rol_id', $rol->id)->update(['rol' => $rol->nombre]);
        }
        // DB::statement("UPDATE usuarios u JOIN roles r ON u.rol_id = r.id SET u.rol = r.nombre");

        // 3. Drop rol_id column
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn('rol_id');
        });
    }
}
