<?php

require dirname(__DIR__) . '/general_requires/_header.php';

if(isset($_GET['fundation_id']))
{
    $fundation_id = $_GET['fundation_id'];

    require 'php/requires/display_functions.php';
    require 'php/requires/db_functions.php';
    require 'php/requires/controller_functions.php';

    $student_promos = get_student_promos();
    $graduated_promos = get_graduated_promos();
    $sites = get_sites();

    require 'templates/formulaire.php';
}
else
{
    set_alert_style();
    add_error("Vous n'avez pas défini l'id de la fondation en GET");
}
