<?php
// this script is for testing ./errpages/httperror.php, just 
// change the code below
$testcode = 401;
// this just proves we can overwrite $_SERVER
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
// get the code to httperror.php...
putenv("REDIRECT_STATUS={$testcode}");
// test!
require_once './errpages/httperror.php';
?>