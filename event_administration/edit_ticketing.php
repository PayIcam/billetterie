<?php

/**
 * Controlleur de la page permettant de préparer le formulaire de modification d'un évènement. Forcémént, il y a plus de choses à initialiser cette fois ci.
 */

require __DIR__ . '/../general_requires/_header.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        check_user_fundations_rights(get_fundation_id($event_id));

        $event = get_event_details($event_id);

        check_if_event_is_not_too_old($event);

        require 'php/requires/display_functions.php';
        require 'php/requires/db_functions.php';
        require 'php/requires/controller_functions.php';
        require 'templates/add_option.php';

        $event['ticketing_start_date'] = date('m/d/Y h:i a', strtotime($event['ticketing_start_date']));
        $event['ticketing_end_date'] = date('m/d/Y h:i a', strtotime($event['ticketing_end_date']));

        $student_promos = array_column(get_student_promos(), 'promo_name');
        $graduated_promos = array_column(get_graduated_promos(), 'promo_name');
        $sites = array_column(get_sites(), 'site_name');

        $promos_specifications = get_specification_details($event_id);
        $removed_promos_specifications = get_removed_specification_details($event_id);
        $event_radios = get_event_radio_values($promos_specifications);
        $options = get_current_options($event_id);

        if(count($options)>0)
        {
            $opt = array('options' => 1);
        }
        else
        {
            $opt = array('options' => 0);
        }
        $event_radios = array_merge($event_radios, $opt);

        require 'templates/formulaire_billetterie.php';
    }
}
else
{
    set_alert_style("Erreur routing");
    add_alert("event_id n'est pas défini en GET");
}