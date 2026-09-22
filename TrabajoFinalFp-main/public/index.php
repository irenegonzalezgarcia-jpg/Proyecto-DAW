<?php
// Controlador Frontal
session_start();

// Definimos la ruta base de la aplicación
define('BASE_PATH', dirname(__DIR__));

// Autocarga básica de clases
spl_autoload_register(function ($class) {
    // Convertir los namespaces al formato de directorio
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    // Cambiamos App por app o Core por core
    if (str_starts_with($path, 'App')) {
        $path = 'app' . substr($path, 3);
    } elseif (str_starts_with($path, 'Core')) {
        $path = 'core' . substr($path, 4);
    }
    
    $file = BASE_PATH . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Router;

// Inicializamos el enrutador
$router = new Router();

// Cargamos las rutas
require_once BASE_PATH . '/app/routes.php';

// Obtenemos la URI actual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Despachamos la solicitud
$router->dispatch($uri, $_SERVER['REQUEST_METHOD']);
