
<?php 

$runtime    = plugin_dir_url( __FILE__ )  . 'angularApp/src/runtime.js';
$polyfills  = plugin_dir_url( __FILE__ )  . 'angularApp/src/polyfills.js';
$scripts    = plugin_dir_url( __FILE__ )  . 'angularApp/src/scripts.js';
$main       = plugin_dir_url( __FILE__ )  . 'angularApp/src/main.js';
$styles     = plugin_dir_url( __FILE__ )  . 'angularApp/src/styles.css';

?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>FrontHotelesPlugin</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
<link rel="stylesheet" href="<?=$styles;?>"></head>
<body >
  <app-root></app-root>
<script src="<?=$runtime;?>" defer=""></script><script src="<?=$polyfills;?>" defer=""></script><script src="<?=$scripts?>" defer=""></script><script src="<?=$main?>" defer=""></script></body>
</html>
