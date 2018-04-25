<?php

require dirname(__DIR__) . '/general_requires/_header.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        require 'php/requires/display_functions.php';
        require 'php/requires/db_functions.php';
        require 'php/requires/controller_functions.php';

        $event = get_event_details($event_id);
        $title = "Entrees : " . $event['name'];
        $arrival_number = get_arrival_number($event_id);
        $participants_number = get_current_participants_number($event_id);

        require 'templates/entrees.php';
    }
}
else
{
    set_alert_style('Erreur paramètres');
    add_alert("Vous n'avez pas spécifié pour quel évènement vous vouliez administrer les entrées.");
}