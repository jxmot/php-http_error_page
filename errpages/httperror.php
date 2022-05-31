<?php
// set to true for testing with canned data, otherwise
// testing can be done with ../testhttperror.php
define('_DEBUG', false);

// can't have both!!
define('_IMG_POOL', false);
define('_GRADIENT', true);

// safety check...
if((defined('_IMG_POOL') && defined('_GRADIENT')) && 
   (_IMG_POOL === _GRADIENT)) {
    echo "<h1>Check _IMG_POOL and _GRADIENT!</h1>\n";
    die();
}

// (is part of the name for the image pool file)
define('PAGE_ID', 'httperror');

// was the request over HTTPS or HTTP?
function isHTTPS() {
  return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443);
}

$http_status = 0;

// get ready...
if(defined('_DEBUG') && _DEBUG === true) {
    $http_status = 404;
    $page_redirected_from = '/nowhere.html';
    $server_url = (isHTTPS() ? 'https://' : 'http://') . 'noplace.com/';
} else {
    // here's the http error code...
    $http_status = getenv('REDIRECT_STATUS');
    // this is especially useful with error 404 to indicate the missing page.
    $page_redirected_from = $_SERVER['REQUEST_URI']; 
    $server_url = (isHTTPS() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/';
}

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
define('SRVNAME',   ((isset($_SERVER['SERVER_NAME']) === true) ? $_SERVER['SERVER_NAME']  : 'none'));
define('REMADDR',   ((isset($_SERVER['REMOTE_ADDR']) === true) ? $_SERVER['REMOTE_ADDR']  : 'none'));

function isLive() {
    $ret = ((SRVNAME !== 'localhost') && 
            (SRVNAME !== '127.0.0.1') && 
            // edit as needed
            (SRVNAME !== 'xampp') && 
            // edit the next 2 CIDRs to match your local network
            (cidrmatch(SRVNAME, '192.168.0.0/24') === false) &&
            (cidrmatch(REMADDR, '192.168.0.0/24') === false));

    return $ret;
}

// the "development & test" environment is different 
// from a "live" server
if(isLive() === true) {
    define('_BASE_PATH', '');
} else {
    // edit as needed, this will be used if the 
    // page is being served locally.
    define('_BASE_PATH', '/tests/httperror');
}

// are we using background images?
if(defined('_IMG_POOL') && _IMG_POOL === true) {
    $imagepool = './' . PAGE_ID . '_imagepool.php';
    require_once $imagepool;
}

$error_code = '';
$explanation = '';
// post-error redirection, it will be redirected 
// automatically after `$redirect_delay` seconds.
$redirect_to = '';
$redirect_delay = 10;

define('REDIRECT', 0);
define('ERROR_CODE', 1);
define('EXPLANATION', 2);

$errorcodes  = json_decode(file_get_contents(getenv('ERRPAGE_JSON_PATH') . 'httperror.json'), true);

if(@$errorcodes["{$http_status}"]) {
    $error_code  = $errorcodes["{$http_status}"][ERROR_CODE];
    $explanation = $errorcodes["{$http_status}"][EXPLANATION];
    $redirect_to = $errorcodes["{$http_status}"][REDIRECT];
} else {
    $error_code  = $http_status . ' - Unknown';
	$explanation = 'Something bad happened, sorry!.';
	$redirect_to = '';
}

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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta name="server_https" content="<?php echo (!empty($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'http'); ?>"/>
    <meta name="server_port" content="<?php echo $_SERVER['SERVER_PORT']; ?>"/>

<?php
// this will auto-redirect if redirect_to contains a URL
if(($redirect_to !== '') && (!defined('_DEBUG') || _DEBUG === false)) {
    echo '    <meta http-equiv="Refresh" content="' . $redirect_delay . '; url=' . $redirect_to . '">';
}

function randColor() {
    return '#' . str_pad(dechex(rand(0, 16777215)+rand(0, 255)), 6, '0', STR_PAD_LEFT);
}

$rcolors = '';
$animenab = '';
$bodytag = "<body>\n";

if(defined('_GRADIENT') && _GRADIENT === true) {
    $rcolors = randColor() . ',' . randColor() . ',' . randColor() . ',' . randColor();
    $animenab = "            animation: bg-gradient 10s ease infinite;\n";
    $bodytag = '<body class="site-bg-gradient">' . "\n";
} else {
    $animenab = "            /* _GRADIENT is off */\n";
    if(defined('_IMG_POOL') && _IMG_POOL === true) {
        $bodytag = '<body class="site-bg-image">' . "\n";
    }
}
?>
	<title>OOPS! : <?php echo $error_code;?></title>

    <link rel="stylesheet" href="<?php echo _BASE_PATH;?>/errpages/reseter.css<?php echo $randquery; ?>"/>
    <link rel="stylesheet" href="<?php echo _BASE_PATH;?>/errpages/httperror.css<?php echo $randquery; ?>"/>
    <!-- this styling must be here to utilize PHP for the random background image and 
         for the moving background gradient with random colors -->
    <style type="text/css">
        .site-bg-image {
            background:url(<?php echo (!isset($selectedBg) ? '' : $selectedBg); ?>) no-repeat center center fixed;
        }

        .site-bg-gradient {
            background: linear-gradient(-45deg, <?php echo $rcolors; ?>);
            /* makes it visible */
            background-size: 400% 400%;
<?php
echo $animenab;
?>
        }
    </style>
</head>
<?php
echo $bodytag;
?>
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
    $dbg = (defined('_DEBUG') && _DEBUG === true ? '<strong>DEBUG</strong>' : '');
    echo '<p class="over-dark-bg">' . $dbg . ' Redirecting in ' . $redirect_delay . ' seconds to ' . $redirect_to . '</p>';
}
?>
        </div>
    </div>
</body>
</html>
