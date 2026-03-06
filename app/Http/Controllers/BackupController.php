<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
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

            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get create table statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                echo "DROP TABLE IF EXISTS `{$tableName}`;\n";
                echo $createTable[0]->{'Create Table'} . ";\n\n";

                // Get data in chunks to save memory
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
            
            // Log action
            Bitacora::registrar('BACKUP', 'sistema', Auth::id(), 'Respaldo de base de datos descargado');

        }, 200, $headers);
    }
}
