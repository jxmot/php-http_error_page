<?php
// this script is for testing ./errpages/httperror.php, just 
// change the code below
$testcode = 401;
// NOTE: The version of httperror.php that uses a JSON file 
// for error code content does not use this. Typically this 
// was used only for 404 errors.
$_SERVER['REQUEST_URI'] = '/bad_path/no_resource';
// this just proves we can overwrite $_SERVER
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
// get the code to httperror.php...
putenv("REDIRECT_STATUS={$testcode}");
// test!
putenv("ERRPAGE_JSON_PATH=./errpages/");
require_once './errpages/httperror.php';
//require_once './errpages/_test.php';
?>