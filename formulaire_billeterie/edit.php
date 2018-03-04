<?php
if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];

    require 'config.php';
    require 'php/php_functions.php';
    require 'html/add_option.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    $student_promos = get_student_promos();
    $graduated_promos = get_graduated_promos();
    $sites = get_sites();

    $event_query = $db->prepare('SELECT * FROM events WHERE event_id=:event_id');
    $event_query->execute(array('event_id'=>$event_id));
    $event = $event_query->fetch();

    $event['ticketing_start_date'] = date('d/m/Y h:i a', strtotime($event['ticketing_start_date']));
    $event['ticketing_end_date'] = date('d/m/Y h:i a', strtotime($event['ticketing_end_date']));

    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id');
    $promos_query->execute(array('event_id'=>$event_id));
    $promos_specifications = $promos_query->fetchAll();

    $list_graduated_promos = array();
    foreach($graduated_promos as $graduated_promo)
    {
        array_push($list_graduated_promos, $graduated_promo['promo_name']);
    }
    $event_radios = get_event_radio_values($promos_specifications);

    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id');
    $option_query->execute(array('event_id'=>$event_id));
    $options = $option_query->fetchAll();

    if(count($options)>0)
    {
        $opt = array('options' => 1);
    }
    else
    {
        $opt = array('options' => 0);
    }
    $event_radios = array_merge($event_radios, $opt);

    require 'html/formulaire.php';
}
else
{
    echo 'Définis event_id en get fdp';
}