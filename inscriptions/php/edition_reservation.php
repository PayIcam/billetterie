<?php

/**
 * Page appelée en Ajax lors de l'edition des réservations
 * La réponse à cette page est donnée en Json, parce qu'il faut aussi donner l'url de la transaction, en plus des messages d'erreur ou du message de validation.
 * Comme d'habitude, il faut vérifier toutes les infos, puis ensuite faire les ajouts dans la base de données.
 * Il faut aussi vérifier qu'il n'y a pas déjà de transaction en attente, que les quotas sont bons, et qu'ils ne seraient pas dépassés, etc ...
 * Cette fois ci pourtant, ce n'est pas exactement pareil. En effet, l'Icam ne va pas payer sa place, qu'il a déjà. Il ne peux que rajouter des options, et mettre à jour son numéro de tel.
 * Egalement, il y aura une distinction entre les nouveaux invités et les anciens.
 * On paye la place des nouveaux invités et d'éventuelles options
 * Pour les anciens on mettra juste à jour nom & prénom, et ajoutera si précisé leurs options
 */

require __DIR__ . '/../../general_requires/_header.php';

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
    die();
}

if(!empty($_POST))
{
    $ajax_json_response = array("message" => "" , "transaction_url" => "");
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

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
        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            $check = check_participant_data($icam_data, 'icam', $promo_specifications, 'update');
            $correct = $check['correct'];
            $icam_data = $check['participant_data'];
            if(!$correct)
            {
                echo json_encode($ajax_json_response);
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

                    if(get_whole_current_quota($event_id) + $participant_additions > $event['total_quota'])
                    {
                        add_alert_to_ajax_response('Trop de participants sont rajoutés pour le quota général.');
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    $guests_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    $current_guests_quota = get_current_promo_site_quota(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    if($current_guests_quota > $guests_specifications['quota'] && $participant_additions>0)
                    {
                        add_alert_to_ajax_response("Le quota pour les invités de " . get_site_name($site_id) . " est déjà plein. ");
                        echo json_encode($ajax_json_response);
                        die();
                    }
                    elseif($current_guests_quota + $participant_additions > $guests_specifications['quota'] && $participant_additions>0)
                    {
                        add_alert_to_ajax_response("Le quota pour les invités de " . get_site_name($site_id) . " est déjà plein. ");
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    foreach($previous_guests_data as &$previous_guest_data)
                    {
                        $check = check_participant_data($previous_guest_data, 'guest', $guests_specifications, 'update');
                        $correct = $check['correct'];
                        $previous_guest_data = $check['participant_data'];
                        if(!$correct)
                        {
                            echo json_encode($ajax_json_response);
                            die();
                        }
                    }
                    foreach($new_guests_data as &$new_guest_data)
                    {
                        $check = check_participant_data($new_guest_data, 'guest', $guests_specifications);
                        $correct = $check['correct'];
                        $new_guest_data = $check['participant_data'];
                        if(!$correct)
                        {
                            echo json_encode($ajax_json_response);
                            die();
                        }
                    }
                }
            }
            else
            {
                add_alert_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input de nom 'guests_informations'");
                echo json_encode($ajax_json_response);
                die();
            }
        }
    }
    else
    {
        add_alert_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'icam_informations'");
        echo json_encode($ajax_json_response);
        die();
    }
    if(isset($_POST['total_transaction_price']))
    {
        if($total_price!=$_POST['total_transaction_price'])
        {
            add_alert_to_ajax_response('Le prix total est incorrect.');
            echo json_encode($ajax_json_response);
            die();
        }
    }

    if($icam_data!=false)
    {
        //Mise à jour de l'Icam, et ajout d'options s'il y en a
        $icam_id = $icam_data->participant_id;

        $icam_insertion_data = array(
            "telephone" => $icam_data->telephone,
            "event_id" => $event_id,
            "site_id" => $icam_data->site_id,
            "promo_id" => $icam_data->promo_id,
            "icam_id" => $icam_id
            );
        update_icam_participant($icam_insertion_data);

        $transaction_linked_purchases = array("participant_ids" => array(), "option_ids" => array());

        $guests_event_article_id = get_promo_article_id(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

        $guests_article_id = array();
        $options_articles = array();

        participant_options_handling($event_id, $icam_id, $icam_data->options);

        //Mise à jour et ajout d'options s'il y en a pour chaque ancien invité
        if(count($previous_guests_data)>0 && $previous_guests_data != false)
        {
            foreach($previous_guests_data as $previous_guest_data)
            {
                $guest_id = $previous_guest_data->participant_id;

                $guest_insertion_data = array(
                    "guest_id" => $guest_id,
                    "prenom" => $previous_guest_data->prenom,
                    "nom" => $previous_guest_data->nom,
                    "event_id" => $event_id,
                    "site_id" => $previous_guest_data->site_id,
                    "promo_id" => $previous_guest_data->promo_id
                    );
                update_guest_participant($guest_insertion_data);
                participant_options_handling($event_id, $guest_id, $previous_guest_data->options);
            }
        }

        //Ajout de chaque nouvel invité
        if(count($new_guests_data)>0 && $new_guests_data != false)
        {
            foreach($new_guests_data as $new_guest_data)
            {
                $guest_insertion_data = array(
                    "prenom" => $new_guest_data->prenom,
                    "nom" => $new_guest_data->nom,
                    "is_icam" => 0,
                    "price" => $new_guest_data->event_price,
                    "event_id" => $event_id,
                    "site_id" => $new_guest_data->site_id,
                    "promo_id" => $new_guest_data->promo_id
                    );
                $guest_id = insert_guest_participant($guest_insertion_data);
                insert_icams_guest(array("event_id" => $event_id, "icam_id" => $icam_id, "guest_id" => $guest_id));

                participant_options_handling($event_id, $guest_id, $new_guest_data->options);

                array_push($transaction_linked_purchases["participant_ids"], $guest_id);
            }
            $guests_article_id = array(array($guests_event_article_id, count($new_guests_data)));
        }

        $transaction_articles = array_merge($guests_article_id, $options_articles);

        //Potentiellement du coup, il n'y a rien à payer, la validation n'a été qu'un changement de nom ou de numéro de téléphone.
        //Dans ce cas, on met un message différent, et on ne redirigera pas vers la page de payement, mais vers la page d'accueil
        if(!empty($transaction_articles))
        {
            $transaction = $payutcClient->createTransaction(array(
                "items" => json_encode($transaction_articles),
                "fun_id" => $event['fundation_id'],
                "mail" => $email,
                "return_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id,
                "callback_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id
                ));
            $ajax_json_response = array("message" => "Votre édition a bien été prise en compte !<br>Vous allez être redirigé pour le payement", "transaction_url" => $transaction->url);

            $transaction_data = array("login" => $email, "liste_places_options" => json_encode($transaction_linked_purchases), "price" => $total_price, "payicam_transaction_id" => $transaction->tra_id, "payicam_transaction_url" => $transaction->url, "event_id" => $event_id, "icam_id" => $icam_id);

            insert_transaction($transaction_data);
        }
        else
        {
            $ajax_json_response = array("message" => "Votre édition a bien été prise en compte !<br>Vous n'avez pas pris de nouvelles options payantes.<br>Vous allez être redirigé vers la page d'accueil.", "transaction_url" => $_CONFIG['public_url']);
        }
        echo json_encode($ajax_json_response);
    }
}
else
{
    set_alert_style("Erreur routing");
    add_alert("Vous n'êtes pas censés appeler la page directement.");
}