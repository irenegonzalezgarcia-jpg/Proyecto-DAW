<?php
define('BASE_PATH', __DIR__);
require 'core/Database.php';

$db = \Core\Database::getConnection();
$db->exec("DELETE FROM treatments");
// Reset auto-increment
$db->exec("DELETE FROM sqlite_sequence WHERE name='treatments'");

$treatments = [
    ['Higiene facial oxigenante', 'Elimina impurezas y devuelve la suavidad a la piel.', 'Facial', 45.00, 60], // 1
    ['Radiofrecuencia facial Indiba', 'Devuelve la juventud y firmeza a los tejidos de la piel.', 'Facial', 60.00, 45], // 2
    ['Fototerapia LED', 'Tratamiento para tratar manchas, acné y rejuvenecimiento.', 'Facial', 50.00, 30], // 3
    ['Piel sensible', 'Calma las pieles irritadas y descamadas.', 'Facial', 55.00, 60], // 4
    ['Hidratación con colágeno', 'Mejora la elasticidad y firmeza de la piel.', 'Facial', 65.00, 60], // 5
    ['Equilibrante', 'Purifica la piel, regula la secreción sebácea y minimiza el poro.', 'Facial', 40.00, 45], // 6
    ['Depilación con cera facial', 'Depilación facial respetuosa con tu piel.', 'Facial', 15.00, 15], // 7
    ['Depilación con hilo', 'Técnica rápida e indolora.', 'Facial', 20.00, 20], // 8
    
    ['Radiofrecuencia corporal', 'Recuperar la firmeza y tonicidad de la piel.', 'Corporal', 70.00, 60], // 9
    ['Criolipólisis', 'Reduce la grasa corporal localizada.', 'Corporal', 90.00, 60], // 10
    ['Anticelulítico', 'Ataca la celulitis activando el metabolismo.', 'Corporal', 55.00, 45], // 11
    ['Ondas de choque', 'Eliminar la grasa localizada y mejorar el aspecto de la piel.', 'Corporal', 80.00, 45], // 12
    ['Presoterapia', 'Estimula la circulación mediante botas de compresión.', 'Corporal', 35.00, 30], // 13
    ['Tratamiento LPG', 'Estimulación celular mecánica profunda.', 'Corporal', 65.00, 45], // 14
    ['Depilación con cera corporal', 'Depilación corporal.', 'Corporal', 40.00, 45], // 15
    ['Masajes estéticos', 'Sensación de bienestar y eliminación de imperfecciones.', 'Corporal', 50.00, 60], // 16
    
    ['Luminosidad Vitamina C', 'Tratamiento antioxidante intenso.', 'Facial', 49.00, 60], // 17
    ['Efecto Glass Skin', 'Piel de porcelana ultra hidratada.', 'Facial', 64.00, 60], // 18
    ['Envoltura de Oro y Caviar', 'Lujo nutritivo y reafirmante.', 'Corporal', 79.00, 90], // 19
    ['Detox Carbón Activo', 'Limpieza profunda de poros.', 'Facial', 39.00, 45], // 20
    ['Drenaje Piernas Ligeras', 'Masaje manual efecto frío.', 'Corporal', 35.00, 40], // 21
    ['Super Brillante', 'Combina aparatología con masajes drenantes.', 'Corporal', 45.00, 60], // 22
    ['Renuévate', 'Higiene facial + tratamiento dermoestético + exfoliante.', 'Corporal', 68.00, 90], // 23
    ['Siempre Divina', 'Armonización facial con ácido hialurónico.', 'Facial', 56.00, 60], // 24
    ['Silueta Perfecta', 'Tratamiento de exfoliación sensorial.', 'Corporal', 35.00, 45] // 25
];
$stmtInsert = $db->prepare("INSERT INTO treatments (name, description, category, price, duration_minutes) VALUES (?, ?, ?, ?, ?)");
foreach ($treatments as $t) {
    $stmtInsert->execute($t);
}
echo "Tratamientos insertados.";
