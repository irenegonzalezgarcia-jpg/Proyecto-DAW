<?php
$dir = __DIR__ . '/app/Controllers';
foreach (glob("$dir/*.php") as $file) {
    $content = file_get_contents($file);
    $content = str_replace("header('Location: /", "header('Location: ' . BASE_URL . '/", $content);
    file_put_contents($file, $content);
}
echo "Done.";
