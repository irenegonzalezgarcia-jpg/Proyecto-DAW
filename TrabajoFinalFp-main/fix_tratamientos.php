<?php
$content = file_get_contents('app/Views/tratamientos.php');

$content = str_replace('<h2>Promociones</h2>', '<h2>Tratamientos</h2>', $content);

// Usamos una expresión regular para quitar el span.ahora y dejar el span.antes como el precio normal
$content = preg_replace('/<span class="antes">(\d+€)<\/span>\s*<span class="ahora">(\d+€)<\/span>/', '<span class="ahora">$1</span>', $content);

file_put_contents('app/Views/tratamientos.php', $content);
echo "Done";
