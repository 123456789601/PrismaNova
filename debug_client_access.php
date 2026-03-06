<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $rol = App\Models\Rol::where('nombre', 'cliente')->first();
    echo "Rol 'cliente' ID: " . ($rol ? $rol->id_rol : 'Not Found') . "\n";
    
    if ($rol) {
        $user = App\Models\Usuario::where('rol_id', $rol->id_rol)->first();
        if ($user) {
            echo "Test User: " . $user->email . " (ID: " . $user->id_usuario . ")\n";
            
            // Login user
            Illuminate\Support\Facades\Auth::login($user);
            echo "Logged in as: " . Illuminate\Support\Facades\Auth::user()->email . "\n";
            
            // Create request
            $request = Illuminate\Http\Request::create('/api/productos', 'GET');
            
            // Dispatch request
            $response = $kernel->handle($request);
            
            echo "Status Code: " . $response->getStatusCode() . "\n";
            $content = $response->getContent();
            echo "Content Length: " . strlen($content) . "\n";
            
            $json = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "JSON Valid: Yes\n";
                if (isset($json['data'])) {
                    echo "Products Count: " . count($json['data']) . "\n";
                    if (count($json['data']) > 0) {
                        echo "First Product Name: " . $json['data'][0]['nombre'] . "\n";
                        echo "First Product Image URL: " . ($json['data'][0]['imagen_url'] ?? 'NULL') . "\n";
                    }
                } else {
                    echo "Key 'data' not found in JSON.\n";
                    print_r($json);
                }
            } else {
                echo "JSON Valid: No\n";
                echo "Raw Content (first 500 chars): " . substr($content, 0, 500) . "\n";
            }
            
        } else {
            echo "No user found with role 'cliente'.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
