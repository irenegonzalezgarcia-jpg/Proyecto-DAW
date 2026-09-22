<?php
namespace Core;

class Controller {
    
    // Método para renderizar una vista
    protected function render(string $view, array $data = []): void {
        // Extraemos las variables para que estén disponibles en la vista
        extract($data);
        
        $viewFile = BASE_PATH . "/app/Views/{$view}.php";
        
        if (file_exists($viewFile)) {
            // Empezamos a capturar el contenido de la vista
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Requerimos el layout principal e inyectamos el contenido
            require BASE_PATH . "/app/Views/layouts/main.php";
        } else {
            die("La vista {$view} no existe.");
        }
    }
}
