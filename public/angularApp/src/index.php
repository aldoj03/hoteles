
<?php 
include "../../../../../../wp-load.php";

$base    = plugin_dir_url( __FILE__ )  ;
$runtime    = plugin_dir_url( __FILE__ )  . 'runtime.js';
$polyfills  = plugin_dir_url( __FILE__ )  . 'polyfills.js';
$main       = plugin_dir_url( __FILE__ )  . 'main.js';
$styles     = plugin_dir_url( __FILE__ )  . 'styles.css';
?>




<!doctype html>
<html lang="en">
<head>
  <base href="<?=$base;?>">
  <meta charset="utf-8">
  <title>FrontHotelesPlugin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
<link rel="stylesheet" href="<?=$styles;?>"></head>
<body >
  <app-root></app-root>
<script src="<?=$runtime;?>" defer=""></script><script src="<?=$polyfills;?>" defer=""></script><script src="<?=$main?>" defer=""></script></body>
</html>
