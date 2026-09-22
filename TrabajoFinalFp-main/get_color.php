<?php
$img = imagecreatefrompng('public/imagenes/logo.png');
$rgb = imagecolorat($img, 0, 0);
$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
echo sprintf("#%02x%02x%02x", $r, $g, $b);
?>
