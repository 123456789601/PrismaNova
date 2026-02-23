<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureTestingDatabaseExists();
    }

    protected function ensureTestingDatabaseExists(): void
    {
        if (env('DB_CONNECTION') === 'mysql') {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');
            $db   = env('DB_DATABASE', 'prismanova_test');
            try {
                $pdo = new \PDO("mysql:host={$host};port={$port}", $user, $pass, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            } catch (\Throwable $e) {
                // silent: permitir correr tests aunque no pueda crear DB
            }
        }
    }
}
