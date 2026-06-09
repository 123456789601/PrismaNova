<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

/**
 * Class BackupController
 * 
 * Controlador para la generación de respaldos de base de datos.
 * Permite a los administradores descargar una copia completa de la base de datos en formato SQL.
 */
class BackupController extends Controller
{
    /**
     * Genera y descarga un respaldo completo de la base de datos.
     * 
     * Crea un archivo SQL con la estructura de todas las tablas y sus datos.
     * Solo accesible para usuarios con rol de administrador.
     *
     * @return \Symfony\Component\HttpFoundation\StreamResponse Archivo SQL para descargar.
     */
    public function download()
    {
        if (!Auth::check() || optional(Auth::user()->rol)->nombre !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $dbName = env('DB_DATABASE');
        $filename = "backup-{$dbName}-" . date('Y-m-d_H-i-s') . ".sql";

        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($dbName) {
            echo "-- Backup de la base de datos: {$dbName}\n";
            echo "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Obtener todas las tablas
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Obtener sentencia CREATE TABLE
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                echo "DROP TABLE IF EXISTS `{$tableName}`;\n";
                echo $createTable[0]->{'Create Table'} . ";\n\n";

                // Obtener datos en bloques para ahorrar memoria
                DB::table($tableName)->orderBy(DB::raw('1'))->chunk(100, function ($rows) use ($tableName) {
                    if ($rows->count() > 0) {
                        echo "INSERT INTO `{$tableName}` VALUES \n";
                        $values = [];
                        foreach ($rows as $row) {
                            $rowValues = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $rowValues[] = "NULL";
                                } else {
                                    $rowValues[] = "'" . addslashes($value) . "'";
                                }
                            }
                            $values[] = "(" . implode(", ", $rowValues) . ")";
                        }
                        echo implode(",\n", $values) . ";\n\n";
                    }
                });
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Registrar acción en bitácora
            Bitacora::registrar('BACKUP', 'sistema', Auth::id(), 'Respaldo de base de datos descargado');

        }, 200, $headers);
    }
}
