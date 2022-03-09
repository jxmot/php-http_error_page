<?php
/* ********************************************************
    Error Page Image Pool

    The CSS class `.site-bg-image` MUST remain in the style
    tag within `httperror.php`. It contains a PHP snippet 
    that is used for accessing `$selectedBg` and inserting 
    the image file name.
*/
$bg = array(_BASE_PATH . '/errpages/accidental-slip-542551_1280.jpg',   // pixabay.com
            _BASE_PATH . '/errpages/pexels-george-becker-374918.jpg',   // pexels.com
            _BASE_PATH . '/errpages/mistake-876597_1280.jpg',           // pixabay.com
            _BASE_PATH . '/errpages/mistake-2344150_1280.jpg');         // pixabay.com
$i = rand(0, count($bg)-1);
$selectedBg = "$bg[$i]";
?>