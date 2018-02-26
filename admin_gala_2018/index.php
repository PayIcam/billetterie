<?php

/**
 *
 * Page d'accueil du site.
 *
 * On fait tout d'abord appel à tout un tas d'autres pages, servant à récupérer des fonctions utiles, et à se connecter à la base de données pour récupérér les infos.
 *
 * Ensuite, on définit toute les données dont on a besoin pour la page.
 *
 * Pour finir, on créé l'affichage de la page avec les dernières pages php appelées. Leurs noms décrivent explicitement de quoi elles s'agissent.
 *
 * display_index.php fonctionne sur ce modèle ci, et est composé de sous-pages php qui décrivent en détail chaque partie de l'affichage.
 *
 * Tout le site est codé sur ce modèle là.
 *
 */

session_start();

require 'config.php';
require 'include/db_functions.php';
require 'include/display_functions.php';
require 'include/html/display_html_functions.php';

$bd = connect_to_db($confSQL);

$current_page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
$rows_per_page = (isset($_GET['rows'])) ? intval($_GET['rows']) : 25;
$start_lign = ($current_page-1) * $rows_per_page;

$number_participants = nb_participants();

if (isset($_POST['recherche']))
{
    $invite = determination_recherche($_POST['recherche'], $start_lign, $rows_per_page);
    $number_pages = $_SESSION['count_recherche'] / $rows_per_page;
    $total_number_pages = (gettype($number_pages) == 'integer') ? $number_pages : intval($number_pages + 1);
    unset($_SESSION['count_recherche']);
}
else
{
    $number_pages = $number_participants / $rows_per_page;
    $total_number_pages = (gettype($number_pages) == 'integer') ? $number_pages : intval($number_pages + 1);
    $invite = all_guests($start_lign, $rows_per_page);
}


require 'include/html/header.php';

require 'include/html/display_index.php';

include 'include/html/footer.php';