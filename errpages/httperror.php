<?php
// uncomment for testing
//define('_DEBUG', true);

// (is part of the name for the image pool file)
define('PAGE_ID', 'httperror');

// get ready...
if(defined('_DEBUG') && _DEBUG === true) {
    $page_redirected_from = '/nowhere.html';
    $server_url = 'http://noplace.com/';
} else {
    $page_redirected_from = $_SERVER['REQUEST_URI'];  // this is especially useful with error 404 to indicate the missing page.
    $server_url = 'http://' . $_SERVER['SERVER_NAME'] . '/';
}
$error_code = '';
$explanation = '';
// post-error redirection, place a path to a resource below
// in the `case` statments and it will be redirected to 
// automatically after `$redirect_delay` seconds.
$redirect_to = '';
$redirect_delay = 10;

if(defined('_DEBUG') && _DEBUG === true) {
    $http_status = 404;
} else {
    // here's the http error code...
    $http_status = getenv('REDIRECT_STATUS');
}
// check the server's error code...
switch($http_status) {
	# '400 - Bad Request'
	case 400:
	$error_code = '400 - Bad Request';
	$explanation = 'The syntax of the URL submitted by your browser could not be understood. Please verify the address and try again.';
    // edit as needed for each `$http_status`
	$redirect_to = '';
	break;

	# '401 - Unauthorized'
	case 401:
	$error_code = '401 - Unauthorized';
	$explanation = 'This section requires a password or is otherwise protected. If you feel you have reached this page in error, please return to the login page and try again, or contact the webmaster if you continue to have problems.';
	$redirect_to = '';
	break;

	# '403 - Forbidden'
    # might be handled by the server instead of here
	case 403:
	$error_code = '403 - Forbidden';
	$explanation = 'This section requires a password or is otherwise protected. If you feel you have reached this page in error, please return to the login page and try again, or contact the webmaster if you continue to have problems.';
	$redirect_to = '';
	break;

	# '404 - Not Found'
	case 404:
	$error_code = '404 - Not Found';
	$explanation = 'The requested resource: <span>' . $page_redirected_from . '</span>, could not be found on this server. Please verify the address and try again.';
    if(defined('_DEBUG') && _DEBUG === true) {
        $redirect_to = 'https://google.com';
    } else {
        $redirect_to = '';
    }
	break;

	# '405 - Method Not Allowed'
	case 405:
	$error_code = '405 - Method Not Allowed';
	$explanation = 'The request method is known by the server but has been disabled and cannot be used.';
	$redirect_to = '';
	break;

    # everything else...
    default:
    $error_code = $http_status . ' - Unknown';
	$explanation = 'Something bad happened, sorry!.';
	$redirect_to = '';
    break;
}

define('SRVNAME',   ((isset($_SERVER['SERVER_NAME']) === true) ? $_SERVER['SERVER_NAME']  : 'none'));
define('REMADDR',   ((isset($_SERVER['REMOTE_ADDR']) === true) ? $_SERVER['REMOTE_ADDR']  : 'none'));

/*
    cidrmatch() - returns `true` only if the IP 
    falls within the CIDR
*/
function cidrmatch($ip, $cidr)
{
    list($subnet, $bits) = explode('/', $cidr);
    if($bits === null) {
        $bits = 32;
    }
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    return ($ip & $mask) == $subnet;
}

/*
    isLive() - returns `true` if this script is 
    running on a "live" server
*/
function isLive() {
    $ret = ((SRVNAME !== 'localhost') && 
            (SRVNAME !== '127.0.0.1') && 
            (SRVNAME !== 'xampp') && 
            (cidrmatch(SRVNAME, '192.168.0.0/24') === false) &&
            (cidrmatch(REMADDR, '192.168.0.0/24') === false));

    return $ret;
}

if(isLive() === true) {
    define('_BASE_PATH', '');
} else {
    // edit as needed, this will be used if the 
    // page is being served locally.
    define('_BASE_PATH', '/tests/httperror');
}

$imagepool = './' . PAGE_ID . '_imagepool.php';
require_once $imagepool;

// this will help defeat forced caching, like some android
// browsers. they even ignore the anti-caching meta tags.
$randquery = '?' . (microtime(true) * 10000);
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8"/>
    <meta name="robots" content="noindex,nofollow">	
    <meta name="author" content="Jim Motyl - github.com/jxmot"/>
    <link href="/favicon.ico" rel="icon" type="image/ico" />
<?php
    // this will auto-redirect if redirect_to contains a URL
	if(($redirect_to !== '') && (!defined('_DEBUG') || _DEBUG === false)) {
        echo '<meta http-equiv="Refresh" content="' . $redirect_delay . '; url=' . $redirect_to . '">';
	}
?>
	<title>OOPS! : <?php echo $error_code;?></title>

    <link rel="stylesheet" href="<?php echo _BASE_PATH;?>/errpages/reseter.css<?php echo $randquery; ?>"/>
    <link rel="stylesheet" href="<?php echo _BASE_PATH;?>/errpages/httperror.css<?php echo $randquery; ?>"/>
    <!-- this styling must be here to utilize PHP for the random background image -->
    <style type="text/css">
        .site-bg-image {
            background:url(<?php echo $selectedBg; ?>) no-repeat center center fixed;
            z-index: -1;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size:cover;
        }
    </style>
</head>
<body class="site-bg-image">
    <div class="httperror-banner httperror-banner-border httperror-banner-shadow">
        <div class="httperror-content">
            <h1 id="errcode" class="over-dark-bg httperror-heading">Error Code: <?php print ($error_code); ?></h1>
            <p id="explan" class="over-dark-bg"><?PHP echo($explanation); ?></p>
            <p id="trythis" class="over-dark-bg">
                You may also want to try starting from the home page: 
                <a href="<?php print ($server_url); ?>"><?php print ($server_url); ?></a>
            </p>
<?php
    // this will auto-redirect if $redirect_to contains a URL
	if($redirect_to != '') {
        echo '            ';
        echo '<p class="over-dark-bg">Redirecting in ' . $redirect_delay . ' seconds to ' . $redirect_to . '</p>';
	}
?>
        </div>
    </div>
</body>
</html>
