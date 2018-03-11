<?php
if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];

    require '../config.php';
    require '../general_requires/db_functions.php';
    require '../general_requires/display_functions.php';
    require 'php/requires/display_functions.php';
    require 'php/requires/db_functions.php';
    require 'php/requires/controller_functions.php';
    require 'templates/add_option.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    $student_promos = get_student_promos();
    $graduated_promos = get_graduated_promos();
    $sites = get_sites();

    $event = get_event_details($event_id);

    $event['ticketing_start_date'] = date('d/m/Y h:i a', strtotime($event['ticketing_start_date']));
    $event['ticketing_end_date'] = date('d/m/Y h:i a', strtotime($event['ticketing_end_date']));

    $promos_specifications = get_specification_details($event_id);

    $list_graduated_promos = array();
    foreach($graduated_promos as $graduated_promo)
    {
        array_push($list_graduated_promos, $graduated_promo['promo_name']);
    }
    $event_radios = get_event_radio_values($promos_specifications);

    $options = get_options($event_id);

    if(count($options)>0)
    {
        $opt = array('options' => 1);
    }
    else
    {
        $opt = array('options' => 0);
    }
    $event_radios = array_merge($event_radios, $opt);

    require 'templates/formulaire.php';
}
else
{
    echo 'Définis event_id en get fdp';
}