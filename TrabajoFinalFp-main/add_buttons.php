<?php
$map = [
    'Higiene facial oxigenante' => 1,
    'Radiofrencuencia facial Indiba ' => 2, // Typo in original text
    'Radiofrecuencia facial Indiba' => 2,
    'Fototerapia LED' => 3,
    'Piel sensible' => 4,
    'Hidratación con colágeno' => 5,
    'Equilibrante' => 6,
    'Depilación con cera' => 7,
    'Depilación con hilo' => 8,
    
    'Radiofrecuencia corporal' => 9,
    'Criolipólisis' => 10,
    'Anticelulítico' => 11,
    'Ondas de choque' => 12,
    'Presoterapia' => 13,
    'Tratamiento LPG' => 14,
    'Depilación con cera' => 15,
    'Masajes estéticos' => 16,
    
    'Luminosidad Vitamina C' => 17,
    'Efecto Glass Skin' => 18,
    'Envoltura de Oro y Caviar' => 19,
    'Detox Carbón Activo' => 20,
    'Drenaje Piernas Ligeras' => 21,
    'Super Brillante' => 22,
    'Renuévate' => 23,
    'Siempre Divina' => 24,
    'Silueta Perfecta' => 25
];

function injectButtons($file, $map, $isPromo = false) {
    $content = file_get_contents($file);
    
    if ($isPromo) {
        // Promociones has <div class="precio-promo"> ... </div>
        // We match <h3>Title</h3> and inject form after precio-promo
        $content = preg_replace_callback('/(<h3>(.*?)<\/h3>.*?<div class="precio-promo">.*?<\/div>)/s', function($m) use ($map) {
            $title = trim($m[2]);
            $id = $map[$title] ?? 0;
            if ($id > 0) {
                $form = "\n<form action=\"/cesta/add\" method=\"POST\" style=\"margin-top:10px;\"><input type=\"hidden\" name=\"treatment_id\" value=\"$id\"><button type=\"submit\" style=\"background-color:#c4a47c; color:#fff; border:none; padding:8px 15px; cursor:pointer; font-weight:bold;\">Añadir a la cesta</button></form>";
                return $m[1] . $form;
            }
            return $m[1];
        }, $content);
    } else {
        // Facial and Corporal have <h3>Title</h3> \n <p>...</p>
        $content = preg_replace_callback('/(<h3>(.*?)<\/h3>\s*<p>.*?<\/p>)/s', function($m) use ($map) {
            $title = trim($m[2]);
            // Special case for Depilación con cera in corporal.php vs facial.php handled by checking file name or ID
            $id = $map[$title] ?? 0;
            if ($title === 'Depilación con cera' && strpos($m[0], 'corporal') !== false) {
                $id = 15; // Corporal
            }
            if ($id > 0) {
                $form = "\n<form action=\"/cesta/add\" method=\"POST\" style=\"margin-top:10px;\"><input type=\"hidden\" name=\"treatment_id\" value=\"$id\"><button type=\"submit\" style=\"background-color:#c4a47c; color:#fff; border:none; padding:8px 15px; cursor:pointer; font-weight:bold;\">Añadir a la cesta</button></form>";
                return $m[1] . $form;
            }
            return $m[1];
        }, $content);
    }
    
    file_put_contents($file, $content);
}

$dir = __DIR__ . '/app/Views/';
injectButtons($dir . 'facial.php', $map);
injectButtons($dir . 'corporal.php', $map);
injectButtons($dir . 'promociones.php', $map, true);

echo "Botones inyectados.";
