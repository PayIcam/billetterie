<?php

require '../../general_requires/display_functions.php';

if(!empty($_POST))
{
    // var_dump($_POST);
    // require '../../DB.php';
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

    check_if_event_should_be_displayed($event,$promo_id, $site_id, $email);

    $promo_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id));

    $total_price = 0;

    if(isset($_POST['icam_informations']))
    {
        if(get_current_promo_quota(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id)) + 1 > $promo_specifications['quota'])
        {
            add_error("Le quota pour les " . $promo . " de " . $site . " est déjà plein. ");
            die();
        }

        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            if(!is_correct_participant_data($icam_data, 'icam', $promo_specifications))
            {
                die();
            }
            if(isset($_POST['guests_informations']))
            {
                $guests_data = json_decode_particular($_POST['guests_informations']);

                $participant_additions = 1 + count($guests_data);

                if(get_current_participants_number($event_id) + $participant_additions > $event['total_quota'])
                {
                    add_error('Trop de participants sont rajoutés pour le quota général.');
                    die();
                }

                if($guests_data!=false)
                {

                    $guests_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    if(get_current_promo_quota(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id)) + count($guests_data) > $guests_specifications['quota'])
                    {
                        add_error("Le quota pour les invités de " . $site . " est déjà plein. ");
                        die();
                    }

                    foreach($guests_data as $guest_data)
                    {
                        if(!is_correct_participant_data($guest_data, 'guest', $guests_specifications))
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
    else
    {
        add_error("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'total_transaction_price'");
        die();
    }

    if($icam_data!=false)
    {
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

        if(count($guests_data)>0 && $guests_data != false)
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
    echo "Votre réservation a bien été prise en compte !";
}
else
{
    set_alert_style();
    add_error("Vous n'êtes pas censés appeler la page directement.");
}