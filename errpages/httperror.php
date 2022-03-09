<?php
// (is part of the name for the image pool file)
define('PAGE_ID', 'httperror');

// get ready...
$page_redirected_from = $_SERVER['REQUEST_URI'];  // this is especially useful with error 404 to indicate the missing page.
$server_url = 'http://' . $_SERVER['SERVER_NAME'] . '/';
$error_code = '';
$explanation = '';
// post-error redirection, place a path to a resource below
// in the `case` statments and it will be redirected to 
// automatically after `$redirect_delay` seconds.
$redirect_to = '';
$redirect_delay = 10;
// here's the http error code...
$http_status = getenv('REDIRECT_STATUS');
// check the server's error code...
switch($http_status)
{
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
	$explanation = 'The requested resource: ' . $page_redirected_from . ', could not be found on this server. Please verify the address and try again.';
	$redirect_to = '';
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
} else define('_BASE_PATH', '/tests/httperror');

$imagepool = './' . PAGE_ID . '_imagepool.php';
require_once $imagepool;
?>

<!DOCTYPE html>
<head>
    <meta name="robots" content="noindex,nofollow">	
    <meta name="author" content="Jim Motyl - github.com/jxmot"/>
    <link href="/favicon.ico" rel="icon" type="image/ico" />
<?php
    // this will auto-redirect if redirect_to contains a URL
	if ($redirect_to != "")
	{
        echo '<meta http-equiv="Refresh" content="' . $redirect_delay . '; url=' . $redirect_to . '">';
	}
?>
	<title>OOPS! : <?php echo $error_code;?></title>

    <style type="text/css">/*! normalize.css v5.0.0 | MIT License | github.com/necolas/normalize.css */html{font-family:sans-serif;line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,footer,header,nav,section{display:block}h1{font-size:2em;margin:.67em 0}figcaption,figure,main{display:block}figure{margin:1em 40px}hr{box-sizing:content-box;height:0;overflow:visible}pre{font-family:monospace,monospace;font-size:1em}a{background-color:transparent;-webkit-text-decoration-skip:objects}a:active,a:hover{outline-width:0}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:inherit}b,strong{font-weight:bolder}code,kbd,samp{font-family:monospace,monospace;font-size:1em}dfn{font-style:italic}mark{background-color:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}audio,video{display:inline-block}audio:not([controls]){display:none;height:0}img{border-style:none}svg:not(:root){overflow:hidden}button,input,optgroup,select,textarea{font-family:sans-serif;font-size:100%;line-height:1.15;margin:0}button,input{overflow:visible}button,select{text-transform:none}[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:1px dotted ButtonText}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}progress{display:inline-block;vertical-align:baseline}textarea{overflow:auto}[type=checkbox],[type=radio]{box-sizing:border-box;padding:0}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-cancel-button,[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}details,menu{display:block}summary{display:list-item}canvas{display:inline-block}template{display:none}[hidden]{display:none}/*! Simple HttpErrorPages | MIT X11 License | https://github.com/AndiDittrich/HttpErrorPages */body,html{width:100%;height:100%;background-color:#21232a}body{color:#fff;text-align:center;text-shadow:0 2px 4px rgba(0,0,0,.5);padding:0;min-height:100%;-webkit-box-shadow:inset 0 0 100px rgba(0,0,0,.8);box-shadow:inset 0 0 100px rgba(0,0,0,.8);display:table;font-family:"Open Sans",Arial,sans-serif}h1{font-family:inherit;font-weight:500;line-height:1.1;color:inherit;font-size:36px}h1 small{font-size:68%;font-weight:400;line-height:1;color:#777}a{text-decoration:none;color:#fff;font-size:inherit;border-bottom:dotted 1px #707070}.lead{color:silver;font-size:21px;line-height:1.4}.cover{display:table-cell;vertical-align:middle;padding:0 20px}footer{position:fixed;width:100%;height:40px;left:0;bottom:0;color:#a0a0a0;font-size:14px}</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/cyborg/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Saira+Stencil+One">
    <link rel="stylesheet" href="<?php echo _BASE_PATH;?>/errpages/httperror.css"/>
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
        <div class="row">
            <div class="httperror-content">
                <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h1 class="over-dark-bg httperror-heading">Error Code: <?php print ($error_code); ?></h1>
                            <p class="over-dark-bg"><?PHP echo($explanation); ?></p>
                            <p class="over-dark-bg">
                                You may also want to try starting from the home page: 
                                <a href="<?php print ($server_url); ?>"><?php print ($server_url); ?></a>
                            </p>
<?php
    // this will auto-redirect if $redirect_to contains a URL
	if ($redirect_to != "")
	{
        echo '                            ';
        echo '<p class="over-dark-bg">Redirecting in ' . $redirect_delay . ' seconds to ' . $redirect_to . '</p>';
	}
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
