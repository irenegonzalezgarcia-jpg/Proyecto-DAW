<?php
function stripForms($file) {
    $c = file_get_contents($file);
    $c = preg_replace('/<form action="\/cesta\/add".*?<\/form>/s', '', $c);
    file_put_contents($file, $c);
}
$dir = __DIR__ . '/app/Views/';
stripForms($dir . 'facial.php');
stripForms($dir . 'corporal.php');
echo "Forms removed.";
