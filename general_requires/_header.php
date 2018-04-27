<?php

/*Cette page est le middleware de mon application.
Toutes les pages appelées vont elles-mêmes l'appeler, afin d'initialiser les bonnes choses.

On retrouve notamment l'initialisation de :
- La varaible de config $_CONFIG, prenant ses infos de config.php
- La varaible de session $_SESSION, remplie au fur et à mesure
- La variable d'Authentification, apportant quelques infos utiles sur le User connecté : $Auth, de la Classe Auth.php
- Le PayutcJsonClient, avec la variable $payUtcClient, initialisé selon la page appelée au service souhaité

De plus, on vérifie à chaque fois que l'on est bien connecté. Si ce n'est pas le cas, on est redirigé vers Le Cas pour se faire. Si c'est le cas, on laisse faire, et on affiche la page.

*/

session_start();

$rootPath = dirname(__DIR__) . '/';
require $rootPath . 'vendor/autoload.php';

require $rootPath . 'general_requires/db_functions.php';
require $rootPath . 'general_requires/display_functions.php';
require $rootPath . 'general_requires/controller_functions.php';

$_CONFIG = require $rootPath. '/config.php';
$confSQL = $_CONFIG['ticketing'];

$DB = new \CoreHelpers\DB($confSQL['sql_host'],$confSQL['sql_user'],$confSQL['sql_pass'],$confSQL['sql_db']);
$db = $DB->db;

/**
 * Cette fonction permet de se connecter au JSON client
 * @param  [string] $service c'est le nom du service permettant de se connecter.
 * @return objet json client
 */
function getPayutcClient($service)
{
    global $_CONFIG;
    return new \JsonClient\AutoJsonClient(
        $_CONFIG['payicam']['url'],
        $service,
        array(),
        "PayIcam Json PHP Client",
        $_SESSION['payutc_cookie'] ?? "");
}

$route = str_replace($_CONFIG['base_path'], '', $_SERVER['REQUEST_URI']);
$route = current(explode('?', $route, 2));

switch($_SERVER['REQUEST_URI'])
{
    case strpos($_SERVER['REQUEST_URI'], '/event_administration/') !== false:
        $payutcClient = getPayutcClient("GESARTICLE");
        if(isset($_SESSION['icam_informations']))
        {
            redirect_if_not_admin($payutcClient->isSuperAdmin());
            $fundations = redirect_if_no_rights();
        }
        break;
    case strpos($_SERVER['REQUEST_URI'], '/participant_administration/') !== false:
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

$icam_informations = null;

$Auth = new \CoreHelpers\Auth();

// Vérifier que l'on est connecté, que l'application est connectée, etc ...
require $rootPath. 'general_requires/validate_auth.php';
