<?php

require __DIR__ . '/../../general_requires/_header.php';

if(!empty($_POST))
{
    $ajax_json_response = array("message" => "" , "transaction_url" => "");

    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $_SESSION['icam_informations']->promo_id;
    $site_id = $_SESSION['icam_informations']->site_id;

    $event_id = $_GET['event_id'] ?? "no_GET";
    if(!event_id_is_correct($event_id))
    {
        echo json_encode($ajax_json_response);
        die();
    }

    handle_pending_reservations($email, $event_id);

    $event = get_event_details($event_id);

    check_if_event_should_be_displayed($event,$promo_id, $site_id, $email);

    $promo_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id));

    $total_price = 0;

    if(isset($_POST['icam_informations']))
    {
        if(get_current_promo_quota(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id)) + 1 > $promo_specifications['quota'])
        {
            add_error_to_ajax_response("Le quota pour les " . $promo . " de " . $site . " est déjà plein. ");
            echo json_encode($ajax_json_response);
            die();
        }

        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            if(!is_correct_participant_data($icam_data, 'icam', $promo_specifications))
            {
                echo json_encode($ajax_json_response);
                die();
            }
            if(isset($_POST['guests_informations']))
            {
                $guests_data = json_decode_particular($_POST['guests_informations']);

                $participant_additions = 1 + count($guests_data);

                if(get_current_participants_number($event_id) + $participant_additions > $event['total_quota'])
                {
                    add_error_to_ajax_response('Trop de participants sont rajoutés pour le quota général.');
                    echo json_encode($ajax_json_response);
                    die();
                }

                if($guests_data!=false)
                {
                    $guests_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    if(get_current_promo_quota(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id)) + count($guests_data) > $guests_specifications['quota'])
                    {
                        add_error_to_ajax_response("Le quota pour les invités de " . $site . " est déjà plein. ");
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    foreach($guests_data as $guest_data)
                    {

                        if(!is_correct_participant_data($guest_data, 'guest', $guests_specifications))
                        {
                            echo json_encode($ajax_json_response);
                            die();
                        }
                    }
                }
            }
            else
            {
                add_error_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input de nom 'guests_informations'");
                echo json_encode($ajax_json_response);
                die();
            }
        }
    }
    else
    {
        add_error_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'icam_informations'");
        echo json_encode($ajax_json_response);
        die();
    }
    if(isset($_POST['total_transaction_price']))
    {
        if($total_price!=$_POST['total_transaction_price'])
        {
            add_error_to_ajax_response('Le prix total est incorrect.');
            echo json_encode($ajax_json_response);
            die();
        }
    }
    else
    {
        add_error_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'total_transaction_price'");
        echo json_encode($ajax_json_response);
        die();
    }


    if($icam_data!=false)
    {
        $icam_data->telephone = $icam_data->telephone == '' ? null : $icam_data->telephone;

        $icam_insertion_data = array(
            "prenom" => $icam_data->prenom,
            "nom" => $icam_data->nom,
            "is_icam" => $icam_data->is_icam,
            "email" => $icam_data->email,
            "price" => $icam_data->price,
            "telephone" => $icam_data->telephone,
            "event_id" => $event_id,
            "site_id" => $icam_data->site_id,
            "promo_id" => $icam_data->promo_id
            );
        $icam_id = insert_icam_participant($icam_insertion_data);

        $transaction_linked_purchases = array("participant_ids" => array($icam_id), "option_ids" => array());

        $icam_event_article_id = array(array($icam_data->icam_event_article_id, 1));
        $guests_article_id = array();
        $options_articles = array();

        participant_options_handling($event_id, $icam_id, $icam_data->options);

        if(count($guests_data)>0 && $guests_data != false)
        {
            foreach($guests_data as $guest_data)
            {

                $guest_insertion_data = array(
                    "prenom" => $guest_data->prenom,
                    "nom" => $guest_data->nom,
                    "is_icam" => $guest_data->is_icam,
                    "price" => $guest_data->price,
                    "event_id" => $event_id,
                    "site_id" => $guest_data->site_id,
                    "promo_id" => $guest_data->promo_id
                    );
                $guest_id = insert_guest_participant($guest_insertion_data);
                insert_icams_guest(array("event_id" => $event_id, "icam_id" => $icam_id, "guest_id" => $guest_id));

                participant_options_handling($event_id, $guest_id, $guest_data->options);

                array_push($transaction_linked_purchases["participant_ids"], $guest_id);
            }
            $guests_article_id = array(array($guest_data->guest_event_article_id, count($guests_data)));
        }
        $transaction_articles = array_merge($icam_event_article_id, $guests_article_id, $options_articles);

        $transaction = $payutcClient->createTransaction(array(
            "items" => json_encode($transaction_articles),
            "fun_id" => $event['fundation_id'],
            "mail" => $email,
            "return_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id,
            "callback_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id
            ));

        $ajax_json_response = array("message" => "Votre réservation a bien été prise en compte ! <br>Vous allez être redirigé pour payer !", "transaction_url" => $transaction->url);

        $transaction_data = array("login" => $email, "liste_places_options" => json_encode($transaction_linked_purchases), "price" => $total_price, "payicam_transaction_id" => $transaction->tra_id, "payicam_transaction_url" => $transaction->url, "event_id" => $event_id, "icam_id" => $icam_id);

        insert_transaction($transaction_data);

        echo json_encode($ajax_json_response);
    }
}
else
{
    set_alert_style();
    add_error("Vous n'êtes pas censés appeler la page directement.");
}