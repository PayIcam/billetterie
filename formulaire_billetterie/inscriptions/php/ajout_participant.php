<?php

require '../../config.php';
require '../../general_requires/db_functions.php';
require '../../general_requires/display_functions.php';
require 'requires/db_functions.php';
require 'requires/controller_functions.php';

$db = connect_to_db($_CONFIG['ticketing']);

if(isset($_POST))
{
    var_dump($_POST);

    $event_id = $_GET['event_id'] ?? "no_GET";
    if(!event_id_is_correct($event_id))
    {
        die;
    }

    if(isset($_POST['icam_informations']))
    {
        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            if(!is_correct_participant_data($icam_data, 'icam'))
            {
                die();
            }
        }
    }
    else
    {
        echo "Quelqu'un s'est débrouillé pour supprimer l'input de nom 'icam_informations'";
    }

    if(isset($_POST['guests_informations']))
    {
        $guests_data = json_decode_particular($_POST['guests_informations']);
        if($guests_data!=false)
        {
            foreach($guests_data as $guest_data)
            {
                if(!is_correct_participant_data($guest_data, 'guest'))
                {
                    die();
                }
            }
        }
    }
    else
    {
        echo "Quelqu'un s'est débrouillé pour supprimer l'input de nom 'guests_informations'";
    }

    $icam_data->birthdate = $icam_data->birthdate == '' ? null : $icam_data->birthdate;
    $icam_data->telephone = $icam_data->telephone == '' ? null : $icam_data->telephone;

    $icam_insertion_data = array(
        "prenom" => $icam_data->prenom,
        "nom" => $icam_data->nom,
        "is_icam" => $icam_data->is_icam,
        "email" => $icam_data->email,
        "price" => $icam_data->price,
        "telephone" => $icam_data->telephone,
        "birthdate" => $icam_data->birthdate,
        "event_id" => $event_id,
        "site_id" => $icam_data->site_id,
        "promo_id" => $icam_data->promo_id
        );
    insert_icam_participant($icam_insertion_data);
    $icam_id = $db->lastInsertId();

    participant_options_handling($event_id, $icam_id, $icam_data->options);

    if(count($guests_data)>0)
    {
        foreach($guests_data as $guest_data)
        {
            $guest_data->birthdate = $guest_data->birthdate == '' ? null : $guest_data->birthdate;

            $guest_insertion_data = array(
                "prenom" => $guest_data->prenom,
                "nom" => $guest_data->nom,
                "is_icam" => $guest_data->is_icam,
                "price" => $guest_data->price,
                "birthdate" => $guest_data->birthdate,
                "event_id" => $event_id,
                "site_id" => $guest_data->site_id,
                "promo_id" => $guest_data->promo_id
                );
            insert_guest_participant($guest_insertion_data);
            $guest_id = $db->lastInsertId();
            insert_icams_guest(array("event_id" => $event_id, "icam_id" => $icam_id, "guest_id" => $guest_id));

            participant_options_handling($event_id, $guest_id, $guest_data->options);
        }
    }
}
else
{
    echo "Vous n'êtes pas censés appeler la page directement.";
}