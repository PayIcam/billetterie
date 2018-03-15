<?php

require 'config.php';
require 'general_requires/db_functions.php';
require 'general_requires/display_functions.php';
require 'homepage/requires/display_functions.php';
require 'homepage/requires/db_functions.php';
require 'homepage/requires/controller_functions.php';

$email = 'gregoire.giraud@2020.icam.fr';

session_start();

$db = connect_to_db($_CONFIG['ticketing']);

$icam_informations = json_decode(file_get_contents($_CONFIG['ginger']['url'].$email."/?key=".$_CONFIG['ginger']['key']));

$_SESSION['icam_informations'] = $icam_informations;
$promo_id = get_promo_id($icam_informations->promo);
$site_id = get_site_id($icam_informations->site);
$_SESSION['icam_informations']->promo_id = $promo_id;
$_SESSION['icam_informations']->site_id = $site_id;

$events_id_accessible = get_promos_events(array("promo_id" => $promo_id, "site_id" => $site_id));

require 'homepage/templates/homepage.php';