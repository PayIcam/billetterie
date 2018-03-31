<?php
require __DIR__ . '/general_requires/_header.php';

if(isset($_SESSION['REQUEST_URI']))
{
    header('Location: '. $_SESSION['REQUEST_URI']);
    unset($_SESSION['REQUEST_URI']);
    die();
}

// homepage
require 'homepage/requires/display_functions.php';
require 'homepage/requires/db_functions.php';
require 'homepage/requires/controller_functions.php';

$promo_id = $_SESSION['icam_informations']->promo_id;
$site_id = $_SESSION['icam_informations']->site_id;

$events_id_accessible = get_promos_events(array("promo_id" => $promo_id, "site_id" => $site_id));

require 'homepage/templates/homepage.php';