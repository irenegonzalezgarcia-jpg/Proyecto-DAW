<?php
$db = new PDO('sqlite:database/inspire_beauty.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$images = [
    '/imagenes/pexels-alesiakozik-7795827.jpg',
    '/imagenes/pexels-amar-20861923.jpg',
    '/imagenes/pexels-anntarazevich-6560304.jpg',
    '/imagenes/pexels-arina-krasnikova-6663369.jpg',
    '/imagenes/pexels-arina-krasnikova-6663374.jpg',
    '/imagenes/pexels-artempodrez-7233312.jpg',
    '/imagenes/pexels-artempodrez-7233328.jpg',
    '/imagenes/pexels-burakeroglu3-35495081.jpg',
    '/imagenes/pexels-ekaterinamitkina-9898741.jpg',
    '/imagenes/pexels-elly-fairytale-3865560.jpg',
    '/imagenes/pexels-enginakyurt-4170175.jpg',
    '/imagenes/pexels-hugo-te-conecta-641939610-17570403.jpg',
    '/imagenes/pexels-inna-rabotyagina-51317378-7952871.jpg',
    '/imagenes/pexels-john-tekeridis-21837-3212179.jpg',
    '/imagenes/pexels-karola-g-6629533.jpg',
    '/imagenes/pexels-koolshooters-7693664.jpg',
    '/imagenes/pexels-marine-fougere-2159237528-35884502.jpg',
    '/imagenes/pexels-rainereckphotography-5573584.jpg',
    '/imagenes/pexels-shiny-diamond-3762563.jpg',
    '/imagenes/pexels-tima-miroshnichenko-6186740.jpg',
    '/imagenes/pexels-zandatsu-16571736.jpg'
];

try {
    $stmt = $db->query("SELECT id FROM treatments");
    $treatments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $db->prepare("UPDATE treatments SET image_url = ? WHERE id = ?");

    $index = 0;
    foreach ($treatments as $treatment) {
        $imageUrl = $images[$index % count($images)];
        $updateStmt->execute([$imageUrl, $treatment['id']]);
        $index++;
    }

    echo "Mapped " . count($treatments) . " treatments to images.\n";
} catch (Exception $e) {
    echo "Error mapping images: " . $e->getMessage() . "\n";
}
?>
