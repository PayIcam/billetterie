<?php

/**
 * Page appelée en Ajax lors de l'inscriptions (pas de réservation déjà)
 * La réponse à cette page est donnée en Json, parce qu'il faut aussi donner l'url de la transaction, en plus des messages d'erreur ou du message de validation.
 * Comme d'habitude, il faut vérifier toutes les infos, puis ensuite faire les ajouts dans la base de données.
 * Il faut aussi vérifier qu'il n'y a pas déjà de transaction en attente, que les quotas sont bons, et qu'ils ne seraient pas dépassés, etc ...
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
        if(get_current_promo_site_quota(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id)) + 1 > $promo_specifications['quota'])
        {
            add_alert_to_ajax_response("Le quota pour les " . $promo . " de " . $site . " est déjà plein. ");
            echo json_encode($ajax_json_response);
            die();
        }

        $icam_data = json_decode_particular($_POST['icam_informations']);
        if($icam_data!=false)
        {
            $check = check_participant_data($icam_data, 'icam', $promo_specifications);
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

                $participant_additions = 1 + ($guests_data == false ? 0 : count($guests_data));

                if(get_whole_current_quota($event_id) + $participant_additions > $event['total_quota'])
                {
                    add_alert_to_ajax_response('Trop de participants sont rajoutés pour le quota général.');
                    echo json_encode($ajax_json_response);
                    die();
                }

                if($guests_data!=false)
                {
                    $guests_specifications = get_promo_specification_details(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));
                    $current_guests_quota = get_current_promo_site_quota(array("event_id" => $event_id, "promo_id" => get_promo_id('Invités'), "site_id" => $site_id));

                    if($current_guests_quota > $guests_specifications['quota'])
                    {
                        add_alert_to_ajax_response("Le quota pour les invités de " . get_site_name($site_id) . " est déjà plein. ");
                        echo json_encode($ajax_json_response);
                        die();
                    }
                    elseif($current_guests_quota + count($guests_data) > $guests_specifications['quota'])
                    {
                        add_alert_to_ajax_response("Vous ajoutez trop d'invités pour le quota d'invités du site de " . get_site_name($site_id) . ".");
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    foreach($guests_data as $guest_data)
                    {
                        $check = check_participant_data($guest_data, 'guest', $guests_specifications);
                        $correct = $check['correct'];
                        $guest_data = $check['participant_data'];
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
    else
    {
        add_alert_to_ajax_response("Quelqu'un s'est débrouillé pour supprimer l'input hidden de nom 'total_transaction_price'");
        echo json_encode($ajax_json_response);
        die();
    }

    if($icam_data!=false)
    {
        $icam_insertion_data = array(
            "prenom" => $_SESSION['icam_informations']->prenom,
            "nom" => $_SESSION['icam_informations']->nom,
            "is_icam" => 1,
            "email" => $_SESSION['icam_informations']->mail,
            "price" => $icam_data->event_price,
            "event_id" => $event_id,
            "site_id" => $icam_data->site_id,
            "promo_id" => $icam_data->promo_id
            );
        $icam_id = insert_icam_participant($icam_insertion_data);
        //Ajout dans participants de l'Icam

        //Ajout de l'id dans le futur JSON parsé en txt pour la table transaction
        $transaction_linked_purchases = array("participant_ids" => array($icam_id), "option_ids" => array());

        $icam_event_article_id = get_promo_article_id(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id));
        $guests_event_article_id = get_promo_article_id(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

        //Préparation de la création de la transaction
        $icam_event_article_id = array(array($icam_event_article_id, 1));
        $guests_article_id = array();
        $options_articles = array();

        //Les options de l'Icam sont ajoutées ici
        participant_options_handling($event_id, $icam_id, $icam_data->options);

        if(count($guests_data)>0 && $guests_data != false)
        {
            //Même fonctionnement pour tous les invités
            foreach($guests_data as $guest_data)
            {
                $guest_insertion_data = array(
                    "prenom" => $guest_data->prenom,
                    "nom" => $guest_data->nom,
                    "is_icam" => 0,
                    "price" => $guest_data->event_price,
                    "event_id" => $event_id,
                    "site_id" => $guest_data->site_id,
                    "promo_id" => $guest_data->promo_id
                    );
                $guest_id = insert_guest_participant($guest_insertion_data);
                insert_icams_guest(array("event_id" => $event_id, "icam_id" => $icam_id, "guest_id" => $guest_id));

                participant_options_handling($event_id, $guest_id, $guest_data->options);

                array_push($transaction_linked_purchases["participant_ids"], $guest_id);
            }
            $guests_article_id = array(array($guests_event_article_id, count($guests_data)));
        }
        //On mets tous les articles payés dans un array, qu'on va envoyer en paramètre pour créer la transaction.
        //Ce sera un array, composé de ce genre d'array : array($id_article, $nombre_articles_payes)
        $transaction_articles = array_merge($icam_event_article_id, $guests_article_id, $options_articles);
        //D'ou le array_merge

        $transaction = $payutcClient->createTransaction(array(
            "items" => json_encode($transaction_articles),
            "fun_id" => $event['fundation_id'],
            "mail" => $email,
            "return_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id,
            "callback_url" => $_CONFIG['public_url']. "inscriptions/php/validate_reservations.php?event_id=".$event_id
            ));

        $transaction_data = array("login" => $email, "liste_places_options" => json_encode($transaction_linked_purchases), "price" => $total_price, "payicam_transaction_id" => $transaction->tra_id, "payicam_transaction_url" => $transaction->url, "event_id" => $event_id, "icam_id" => $icam_id);
        insert_transaction($transaction_data);

        $ajax_json_response = array("message" => "Votre réservation a bien été prise en compte ! <br>Vous allez être redirigé pour payer !", "transaction_url" => $transaction->url);

        echo json_encode($ajax_json_response);
    }
}
else
{
    set_alert_style("Erreur routing");
    add_alert("Vous n'êtes pas censés appeler la page directement.");
}