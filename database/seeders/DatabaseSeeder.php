<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UsuarioAdminSeeder;
use Database\Seeders\CatalogoDemoSeeder;
use Database\Seeders\TransaccionesDemoSeeder;
use Database\Seeders\MetodosPagoSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MetodosPagoSeeder::class,
            UsuarioAdminSeeder::class,
            CatalogoDemoSeeder::class,
            TransaccionesDemoSeeder::class,
        ]);
    }
}
