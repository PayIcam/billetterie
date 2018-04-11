<?php

session_start();

$rootPath = dirname(__DIR__).'/';
require $rootPath. 'vendor/autoload.php';

require $rootPath. 'general_requires/db_functions.php';
require $rootPath. 'general_requires/display_functions.php';
require $rootPath. 'general_requires/controller_functions.php';

$_CONFIG = require $rootPath. '/config.php';
$confSQL = $_CONFIG['ticketing'];

$DB = new \CoreHelpers\DB($confSQL['sql_host'],$confSQL['sql_user'],$confSQL['sql_pass'],$confSQL['sql_db']);
$db = $DB->db;

///////////////////////////
// Autre initialisations //
///////////////////////////

function getPayutcClient($service)
{
    global $_CONFIG;
    return new \JsonClient\AutoJsonClient(
        $_CONFIG['payicam']['url'],
        $service,
        array(),
        "PayIcam Json PHP Client",
        isset($_SESSION['payutc_cookie']) ? $_SESSION['payutc_cookie'] : "");
}
// routes //

$route = str_replace($_CONFIG['base_path'], '', $_SERVER['REQUEST_URI']);
$route = current(explode('?', $route, 2));

switch($_SERVER['REQUEST_URI'])
{
    case strpos($_SERVER['REQUEST_URI'], '/creation/') !== false:
        $payutcClient = getPayutcClient("GESARTICLE");
        if(isset($_SESSION['icam_informations']))
        {
            redirect_if_not_admin($payutcClient->isSuperAdmin());
            $fundations = redirect_if_no_rights();
        }
        break;
    case strpos($_SERVER['REQUEST_URI'], '/entrees/') !== false:
    case strpos($_SERVER['REQUEST_URI'], '/stats/') !== false:
        $payutcClient = getPayutcClient("STATS");
        if(isset($_SESSION['icam_informations']))
        {
            $fundations = redirect_if_no_rights();
        }
        break;
    default:
        $payutcClient = getPayutcClient("WEBSALE");
}

$is_super_admin = $payutcClient->isSuperAdmin();
$is_admin = $payutcClient->isAdmin();
$status = $payutcClient->getStatus();

$casUrl = $payutcClient->getCasUrl()."login?service=".urlencode($_CONFIG['public_url']."login.php");
$logoutUrl = $payutcClient->getCasUrl()."logout?service=".urlencode($_CONFIG['public_url']."login.php");

// Sécurité que des icam
$icam_informations = null;

// On lance la class Auth
$Auth = new \CoreHelpers\Auth();

// On valide que l'on est connecté, & que l'on est bien un icam etc...
require $rootPath. 'general_requires/validate_auth.php';
