<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Illuminate\Support\Facades\DB::select('SHOW CREATE TABLE roles');
print_r($roles);

$usuarios = Illuminate\Support\Facades\DB::select('SHOW CREATE TABLE usuarios');
print_r($usuarios);
