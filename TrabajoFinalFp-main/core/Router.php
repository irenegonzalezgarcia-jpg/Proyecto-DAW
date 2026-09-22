<?php
namespace Core;

class Router {
    private array $routes = [];

    // Definimos una ruta GET
    public function get(string $uri, string $controllerAction): void {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    // Definimos una ruta POST
    public function post(string $uri, string $controllerAction): void {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    // Procesamos y despachamos la petición
    public function dispatch(string $uri, string $method): void {
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        if (isset($this->routes[$method][$uri])) {
            $controllerAction = $this->routes[$method][$uri];
            
            // Dividimos por @ para obtener Clase y Método
            list($controller, $action) = explode('@', $controllerAction);
            $controllerClass = "App\\Controllers\\" . $controller;

            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $action)) {
                    // Ejecutamos la acción del controlador
                    $controllerInstance->$action();
                    return;
                } else {
                    $this->abort(500, "Método {$action} no encontrado en el controlador {$controller}.");
                }
            } else {
                $this->abort(500, "Controlador {$controller} no encontrado.");
            }
        } else {
            $this->abort(404, "Página no encontrada.");
        }
    }

    // Función para abortar la petición con un error
    private function abort(int $code, string $message): void {
        http_response_code($code);
        echo "<h1>Error {$code}</h1>";
        echo "<p>{$message}</p>";
        exit();
    }
}
