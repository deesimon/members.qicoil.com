<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);

$new_width = $_GET['width'];
$new_height = $_GET['height'];

$img = $_GET['img'];

header('Content-Type: image/jpeg');
	$url = $img;
	
	echo $url;die;
	echo file_get_contents($url);
?>