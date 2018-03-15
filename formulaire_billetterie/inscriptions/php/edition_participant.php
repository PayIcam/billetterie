<?php

require '../../general_requires/display_functions.php';
set_alert_style();

if(!empty($_POST))
{
    require '../../config.php';
    require '../../general_requires/db_functions.php';
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    $promo = 120;
    $site = 'Lille';

    $promo_id = get_promo_id($promo);
    $site_id = get_site_id($site);

    $event_id = $_GET['event_id'] ?? "no_GET";
    if(!event_id_is_correct($event_id))
    {
        die();
    }

    $event = get_event_details($event_id);
    $promo_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id));

    $total_price = 0;

    if(isset($_POST['icam_informations']))
    {
        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            if(!is_correct_participant_supplement_data($icam_data, 'icam', $promo_specifications))
            {
                die();
            }
            if(isset($_POST['guests_informations']))
            {
                $guests_data = json_decode_particular($_POST['guests_informations']);
                if($guests_data!=false)
                {
                    $previous_guests_data = $guests_data->previous_guests_data;
                    $new_guests_data = $guests_data->new_guests_data;

                    $participant_additions = count($new_guests_data);

                    if(get_current_participants_number($event_id) + $participant_additions > $event['total_quota'])
                    {
                        add_error('Trop de participants sont rajoutés pour le quota général.');
                        die();
                    }

                    $guests_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    if(get_current_promo_quota(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id)) + $participant_additions > $guests_specifications['quota'])
                    {
                        add_error("Le quota pour les invités de " . $site . " est déjà plein. ");
                        die();
                    }

                    foreach($previous_guests_data as $previous_guest_data)
                    {
                        if(!is_correct_participant_supplement_data($previous_guest_data, 'guest', $guests_specifications))
                        {
                            die();
                        }
                    }
                    foreach($new_guests_data as $new_guest_data)
                    {
                        if(!is_correct_participant_data($new_guest_data, 'guest', $guests_specifications))
                        {
                            die();
                        }
                    }
                }
            }
            else
            {
                add_error("Quelqu'un s'est débrouillé pour supprimer l'input de nom 'guests_informations'");
                die();
            }
        }
    }
    else
    {
        add_error("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'icam_informations'");
        die();
    }
    if(isset($_POST['total_transaction_price']))
    {
        if($total_price!=$_POST['total_transaction_price'])
        {
            add_error('Le prix total est incorrect.');
            die();
        }
    }

    if($icam_data!=false)
    {
        $icam_id = $icam_data->icam_id;

        $icam_data->birthdate = $icam_data->birthdate == '' ? null : $icam_data->birthdate;
        $icam_data->telephone = $icam_data->telephone == '' ? null : $icam_data->telephone;

        $icam_insertion_data = array(
            "price_addition" => $icam_data->price,
            "telephone" => $icam_data->telephone,
            "birthdate" => $icam_data->birthdate,
            "event_id" => $event_id,
            "site_id" => $icam_data->site_id,
            "promo_id" => $icam_data->promo_id,
            "icam_id" => $icam_id
            );
        update_icam_participant($icam_insertion_data);

        participant_options_handling($event_id, $icam_id, $icam_data->options);

        if(count($previous_guests_data)>0 && $previous_guests_data != false)
        {
            foreach($previous_guests_data as $previous_guest_data)
            {
                $previous_guest_data->birthdate = $previous_guest_data->birthdate == '' ? null : $previous_guest_data->birthdate;

                $guest_id = $previous_guest_data->guest_id;

                $guest_insertion_data = array(
                    "guest_id" => $guest_id,
                    "prenom" => $previous_guest_data->prenom,
                    "nom" => $previous_guest_data->nom,
                    "price_addition" => $previous_guest_data->price,
                    "birthdate" => $previous_guest_data->birthdate,
                    "event_id" => $event_id,
                    "site_id" => $previous_guest_data->site_id,
                    "promo_id" => $previous_guest_data->promo_id
                    );
                update_guest_participant($guest_insertion_data);
                participant_options_handling($event_id, $guest_id, $previous_guest_data->options);
            }
        }

        if(count($new_guests_data)>0 && $new_guests_data != false)
        {
            foreach($new_guests_data as $new_guest_data)
            {
                $new_guest_data->birthdate = $new_guest_data->birthdate == '' ? null : $new_guest_data->birthdate;

                $guest_insertion_data = array(
                    "prenom" => $new_guest_data->prenom,
                    "nom" => $new_guest_data->nom,
                    "is_icam" => $new_guest_data->is_icam,
                    "price" => $new_guest_data->price,
                    "birthdate" => $new_guest_data->birthdate,
                    "event_id" => $event_id,
                    "site_id" => $new_guest_data->site_id,
                    "promo_id" => $new_guest_data->promo_id
                    );
                insert_guest_participant($guest_insertion_data);
                $guest_id = $db->lastInsertId();
                insert_icams_guest(array("event_id" => $event_id, "icam_id" => $icam_id, "guest_id" => $guest_id));

                participant_options_handling($event_id, $guest_id, $new_guest_data->options);
            }
        }
    }
}
else
{
    add_error("Vous n'êtes pas censés appeler la page directement.");
}