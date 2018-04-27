<?php

/**
 * Controlleur de la page servant à définir une nouvelle billetterie.
 */

require dirname(__DIR__) . '/general_requires/_header.php';

if(isset($_GET['fundation_id']))
{
    $fundation_id = $_GET['fundation_id'];
    check_user_fundations_rights($fundation_id);

    require 'php/requires/display_functions.php';
    require 'php/requires/db_functions.php';
    require 'php/requires/controller_functions.php';

    $student_promos = array_column(get_student_promos(), 'promo_name');
    $graduated_promos = array_column(get_graduated_promos(), 'promo_name');
    $sites = array_column(get_sites(), 'site_name');

    require 'templates/formulaire_billetterie.php';
}
else
{
    set_alert_style("Erreur routing");
    add_alert("Vous n'avez pas défini l'id de la fondation en GET");
}
