<?php

/**
 * Controlleur de l'Index permettant de montrer aux participants à quels évènements ils peuvent s'inscrire.
 */

require __DIR__ . '/general_requires/_header.php';

if(isset($_SESSION['REQUEST_URI']))
{
    header('Location: '. $_SESSION['REQUEST_URI']);
    unset($_SESSION['REQUEST_URI']);
    die();
}

require 'inscriptions/php/requires/display_functions.php';
require 'inscriptions/php/requires/db_functions.php';
require 'inscriptions/php/requires/controller_functions.php';

$promo_id = $_SESSION['icam_informations']->promo_id;
$site_id = $_SESSION['icam_informations']->site_id;

$events_id_accessible = get_promos_events(array("promo_id" => $promo_id, "site_id" => $site_id));

require 'inscriptions/templates/homepage.php';