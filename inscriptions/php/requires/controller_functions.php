<?php

function option_form($option, $promo_id, $site_id, $participant_id=-1)
{
    global $ticketing_state;
    $already_defined_option = get_participant_option(array("event_id" => $option['event_id'], "option_id" => $option['option_id'], "participant_id" => $participant_id));
    if(!empty($already_defined_option))
    {
        if($option['type']=='Checkbox')
        {
            checkbox_form($option, $already_defined_option['price']);
        }
        elseif($option['type']=='Select')
        {
            $select_choice = get_option_choice($already_defined_option['choice_id']);
            $select_choice['price_paid'] = $already_defined_option['price'];
            select_form($option, $select_choice);
        }
    }
    else
    {
        if($ticketing_state == 'open')
        {
            if($option['is_active']==1)
            {
                $option_quota = $option['quota']==null ? INF : $option['quota'];
                if(get_current_option_quota(array('event_id' => $option['event_id'], 'option_id' => $option['option_id'])) < $option_quota)
                {
                    if(promo_has_option(array("event_id" => $option['event_id'], "option_id" => $option['option_id'], "promo_id" => $promo_id, "site_id" => $site_id)))
                    {
                        if($option['type']=='Checkbox')
                        {
                            checkbox_form($option);
                        }
                        elseif($option['type']=='Select')
                        {
                            select_form($option);
                        }
                    }
                }
            }
        }
    }
}

function participant_options_handling($event_id, $participant_id, $options)
{
    global $options_articles, $transaction_linked_purchases;
    foreach($options as $option)
    {
        $article_id = get_option_article_id($option->choice_id);
        $previous_status = get_participant_previous_option_choice_status(array('event_id' => $event_id, 'participant_id' => $participant_id, 'choice_id' => $option->choice_id));

        if($previous_status!==false)
        {
            update_participant_option_to_waiting(array("event_id" => $event_id, "participant_id" => $participant_id, "choice_id" => $option->choice_id, "price" => $option->price));
        }
        else
        {
            insert_participant_option(array("event_id" => $event_id, "participant_id" => $participant_id, "choice_id" => $option->choice_id, "status" => "W", "price" => $option->price, 'payement' => 'PayIcam'));
        }

        array_push($transaction_linked_purchases["option_ids"], array("participant_id" => $participant_id, "choice_id" => $option->choice_id));

        $found=false;
        foreach($options_articles as &$article)
        {
            if(in_array($article_id, $article))
            {
                $article[1]+=1;
                $found=true;
                break;
            }
        }
        if(!$found)
        {
            array_push($options_articles, array($article_id, 1));
        }
    }
}

function json_decode_particular($data)
{
    if($data == '')
    {
        $data = false;
    }
    else
    {
        $data = json_decode($data);
    }
    return $data;
}

function number_of_guests_to_be_displayed($promo_specifications, $guests_specifications, $current_participants_number, $total_quota, $previous_guests_number=0)
{
    $temporary_guest_number = min($promo_specifications['guest_number']-$previous_guests_number, $total_quota-$current_participants_number);
    $temporary_guest_number = $temporary_guest_number>=0 ? $temporary_guest_number : 0;

    if($temporary_guest_number + $previous_guests_number < $promo_specifications['guest_number'])
    {
        $error_message = "Il n'y a pas assez de places encore disponibles pour tout l'évènement pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_specifications['promo_id']). ".<br>";
        $_SESSION['alert_errors'] = isset($_SESSION['alert_errors']) ? $_SESSION['alert_errors'] . $error_message : $error_message;
    }

    $guest_quota = $guests_specifications['quota'];
    $current_guests_number = get_current_promo_site_quota(array('event_id' => $promo_specifications['event_id'], 'promo_id' => get_promo_id('Invités'), 'site_id' => $promo_specifications['site_id']));

    $actual_guest_number = min($temporary_guest_number, $guest_quota-$current_guests_number);
    $actual_guest_number = $actual_guest_number>=0 ? $actual_guest_number : 0;

    if($actual_guest_number < $temporary_guest_number)
    {
        $error_message = "Il n'y a pas assez de places encore disponibles pour les invités pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_specifications['promo_id']) . ".<br>";
        $_SESSION['alert_errors'] = isset($_SESSION['alert_errors']) ? $_SESSION['alert_errors'] . $error_message : $error_message;
    }

    return $actual_guest_number;
}

function check_participant_options($participant_data, $participant_type, $event_id, $site_id, $promo_id, $error, $left_to_pay)
{
    foreach($participant_data->options as $option)
    {
        if(!is_object($option))
        {
            add_alert_to_ajax_response($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur d'une option <br>");
            $error = true;
        }
        else
        {
            if(isset($option->option_id))
            {
                $option_id = $option->option_id;
                if(!is_an_integer($option_id))
                {
                    add_alert_to_ajax_response($participant_type . " : Impossible d'identifier l'option, qui a un identifiant non entier <br>");
                    $error = true;
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : L'identifiant de l'option n'a pas été transmis <br>");
                return ["error" => true, "left_to_pay" => false];
            }
            if(!promo_has_option(array("event_id" => $event_id, "option_id" => $option_id, "site_id" => $site_id, "promo_id" => $promo_id)))
            {
                add_alert_to_ajax_response($participant_type . " : Cette promotion n'a pas le droit à cette option. <br>");
                $error = true;
            }
            if(isset($option->choice_id))
            {
                $choice_id = $option->choice_id;
                if(!is_correct_choice_id(array('choice_id' => $choice_id, 'option_id' => $option_id)))
                {
                    add_alert_to_ajax_response($participant_type . " : Ce choix n'existe pas <br>");
                    return ["error" => true, "left_to_pay" => false];
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : L'identifiant du choix n'a pas été transmis <br>");
                return ["error" => true, "left_to_pay" => false];
            }

            $option_db_data = get_option(array("event_id" => $event_id, "option_id" => $option_id));

            if($option_db_data['is_active']==0)
            {
                add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " L'option n'est pas active...<br>".$option_id);
                $error = true;
            }

            if(isset($option->type))
            {
                if($option->type != $option_db_data['type'])
                {
                    add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " Quelqu'un s'est débrouillé pour altérer la valeur du type d'une option".$option_id);
                    $error = true;
                }
                else
                {
                    if(get_current_option_quota(array("event_id" => $event_id, "option_id" => $option_id)) +1 > $option_db_data['quota'])
                    {
                        add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Il n'y a plus de places disponibles pour cette option. <br>");
                        $error = true;
                    }
                    if($option_db_data['type']=='Checkbox')
                    {
                        $choice_data = get_checkbox_option_choice($choice_id);
                        if(empty($choice_data))
                        {
                            add_alert_to_ajax_response($participant_type . " : ". $option_db_data['name'] . " : Les informations relatives ont choix sont vides. Ce choix n'est pas un choix de checkbox<br>");
                            return ["error" => true, "left_to_pay" => false];
                        }
                        if(isset($option->name))
                        {
                            if($option_db_data['name'] == $option->name)
                            {
                                if(isset($option->price))
                                {
                                    if($option->price == $choice_data['price'])
                                    {
                                        if($left_to_pay!=false)
                                        {
                                            $left_to_pay-=$option->price;
                                        }
                                    }
                                    else
                                    {
                                        add_alert_to_ajax_response($participant_type . " : ". $option_db_data['name'] . " : Le prix de l'option checkbox est incorrect <br>");
                                        $error = true;
                                    }
                                }
                                else
                                {
                                    add_alert_to_ajax_response($participant_type . " : ". $option_db_data['name'] . " : Le prix de l'option checkbox n'est pas transmis. <br>");
                                    $error = true;

                                }
                            }
                            else
                            {
                                add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Le nom de l'option Checkbox est incorrect <br>");
                                $error = true;
                            }
                        }
                        else
                        {
                            add_alert_to_ajax_response($participant_type . " : Le nom de l'option n'a pas été transmis <br>");
                            $error = true;
                        }
                    }
                    elseif($option_db_data['type']=='Select')
                    {
                        $choice_data = get_select_option_choice($choice_id);
                        if(empty($choice_data))
                        {
                            add_alert_to_ajax_response($participant_type . " : ". $option_db_data['name'] . " : Les informations relatives ont choix sont vides. Ce choix n'est pas un choix de select<br>");
                            return ["error" => true, "left_to_pay" => false];
                        }

                        $select_option_quota = $choice_data['quota']==null ? INF : $choice_data['quota'];
                        if(get_current_select_option_quota(array("event_id" => $event_id, "choice_id" => $choice_id))+1 > $select_option_quota)
                        {
                            add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Le quota d'une sous-option est déjà plein. <br>");
                            $error = true;
                        }

                        if(isset($option->price))
                        {
                            if($option->price == $choice_data['price'])
                            {
                                if($left_to_pay!=false)
                                {
                                    if($choice_data['is_removed']==0)
                                    {
                                        $left_to_pay-=$option->price;
                                    }
                                    else
                                    {
                                        add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Cette sous-option a été enlevée à la vente <br>");
                                        $error = true;
                                    }
                                }
                            }
                            else
                            {
                                add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Le prix d'une sous-option select est faux <br>");
                                $error = true;
                            }
                        }
                        else
                        {
                            add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Le prix d'une sous option select n'est pas transmis <br>");
                            $error = true;
                        }
                    }
                    else
                    {
                        add_alert_to_ajax_response($participant_type . " : Option ". $option_db_data['name'] . " : Le type de l'option n'est ni 'Checkbox', ni 'Select'. Comment est ce arrivé ? <br>");
                        $error = true;
                    }
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : Le type de l'option n'est pas correct <br>");
                $error = true;

            }
        }
    }
    return ["error" => $error, "left_to_pay" => $left_to_pay];
}

function is_correct_participant_data($participant_data, $participant_type, $promo_specifications, $participant_action='addition')
{
    if(!in_array($participant_action, ['addition', 'update']))
    {
        add_alert_to_ajax_response("Erreur du développeur. participant_action doit être soit 'addition', soit 'update' <br>");
        return false;
    }
    if(!in_array($participant_type, ['icam', 'guest']))
    {
        add_alert_to_ajax_response("Erreur du développeur. participant_type doit être soit 'icam', soit 'guest' <br>");
        return false;
    }

    $event_id = $promo_specifications['event_id'];
    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $promo_specifications['promo_id'];
    $site_id = $promo_specifications['site_id'];
    $prenom = $_SESSION['icam_informations']->prenom;
    $nom = $_SESSION['icam_informations']->nom;

    $error = false;
    $left_to_pay = false;
    if($participant_data == null)
    {
        add_alert_to_ajax_response($participant_type . " : POST['".$participant_type."_informations'] est mal défini. Il est impossible de le décoder. <br>");
        $error = true;
    }
    else
    {
        $participant_data_length = $participant_action=='addition' ? ($participant_type=='icam' ? 6:7) : ($participant_type=='icam' ? 6:7);
        if(count(get_object_vars($participant_data)) != $participant_data_length)
        {
            die();
            add_alert_to_ajax_response($participant_type . " : Il n'y a pas le bon nombre d'éléments dans l'objet. <br>");
            $error = true;
        }
        else
        {
            if($participant_action=='update')
            {
                if(isset($participant_data->participant_id))
                {
                    if($participant_type=='icam')
                    {
                        if(!icam_id_is_correct(array('participant_id' => $participant_data->participant_id, 'event_id' => $event_id, 'login' => $email, 'promo_id' => $promo_id, 'site_id' => $site_id)))
                        {
                            add_alert_to_ajax_response($participant_type . " : L'identifiant d'un des participants ayant déjà leur place est incorrect. <br>");
                            $error = true;
                        }
                    }
                    elseif($participant_type=='guest')
                    {
                        if(!guest_id_is_correct(array('participant_id' => $participant_data->participant_id, 'icam_promo_id' => get_promo_id($_SESSION['icam_informations']->promo), 'event_id' => $event_id, 'login' => $email, 'promo_id' => $promo_id, 'site_id' => $site_id)))
                        {
                            add_alert_to_ajax_response($participant_type . " : L'identifiant d'un des participants ayant déjà leur place est incorrect. <br>");
                            $error = true;
                        }
                    }
                }
                else
                {
                    add_alert_to_ajax_response($participant_type . " : L'idenfiant du participant n'a pas été transmis. <br>");
                    $error = true;
                }
            }

            $participant_data_promo_id = $participant_type=='icam' ? $promo_id : get_promo_id('Invités');

            if(isset($participant_data->site_id))
            {
                if($participant_data->site_id != $site_id)
                {
                    add_alert_to_ajax_response($participant_type . " : Le site n'est pas bon. <br>");
                    $error = true;
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : Le site du participant n'a pas été transmis <br>");
                $error = true;
            }
            if(isset($participant_data->promo_id))
            {
                if($participant_data->promo_id != $participant_data_promo_id)
                {
                    add_alert_to_ajax_response($participant_type . " : La promo n'est pas bonne <br>");
                    $error = true;
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : La promo du participant n'a pas été transmise <br>");
                $error = true;
            }
            if($participant_action == 'addition')
            {
                if(isset($participant_data->event_price))
                {
                    if(!is_numeric($participant_data->event_price))
                    {
                        add_alert_to_ajax_response($participant_type . " : Le prix de l'évènement n'est pas numérique<br>");
                        $left_to_pay=false;
                        $error = true;
                    }
                    elseif($participant_data->event_price != $promo_specifications['price'])
                    {
                        add_alert_to_ajax_response($participant_type . " : Le prix de l'évènement est incorrect.<br>");
                        $left_to_pay=false;
                        $error = true;
                    }
                }
                if(isset($participant_data->total_participant_price))
                {
                    if(!is_numeric($participant_data->total_participant_price))
                    {
                        add_alert_to_ajax_response($participant_type . " : Le prix total payé pour le participant n'est pas numérique.<br>");
                        $left_to_pay=false;
                        $error = true;
                    }
                    elseif($participant_data->total_participant_price < $promo_specifications['price'])
                    {
                        add_alert_to_ajax_response($participant_type . " : Le prix total payé par le participant est incohérent (inférieur au prix de l'évènement)<br>");
                        $left_to_pay=false;
                        $error = true;
                    }
                    else
                    {
                        $left_to_pay = $participant_data->total_participant_price-$promo_specifications['price'];
                    }
                }
                else
                {
                    add_alert_to_ajax_response($participant_type . " : Le prix total payé pour le participant n'a pas été transmis <br>");
                    $error = true;
                }
            }
            elseif($participant_action == 'update')
            {
                if(isset($participant_data->participant_price_addition))
                {
                    if(!is_numeric($participant_data->participant_price_addition) || $participant_data->participant_price_addition <0)
                    {
                        add_alert_to_ajax_response($participant_type . " : Le supplément de cout n'est pas numérique, ou négatif <br>");
                        $left_to_pay=false;
                        $error = true;
                    }
                    else
                    {
                        $left_to_pay = $participant_data->participant_price_addition;
                    }
                }
                else
                {
                    add_alert_to_ajax_response($participant_type . " : Le supplément de cout n'est pas transmis <br>");
                    $error = true;
                }
            }
            if($participant_type=='icam')
            {
                if(isset($participant_data->telephone))
                {
                    if(!is_string($participant_data->telephone))
                    {
                        add_alert_to_ajax_response($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du numéro de téléphone <br>");
                        $error = true;
                    }
                    elseif(count($participant_data->telephone)>25)
                    {
                        add_alert_to_ajax_response($participant_type . " : Pourquoi avez vous besoin d'autant de caractères pour un simple numéro de téléphone ?<br>");
                        $error = true;
                    }
                }
                else
                {
                    add_alert_to_ajax_response($participant_type . " : Le numéro de téléphone du participant n'a pas été transmis <br>");
                    $error = true;
                }
            }
            elseif($participant_type=='guest')
            {
                if(!is_string($participant_data->prenom))
                {
                    add_alert_to_ajax_response($participant_type . " : Le prénom n'est pas une chaine de caractères <br>");
                    $error = true;
                }
                elseif(count($participant_data->prenom)>45)
                {
                    add_alert_to_ajax_response($participant_type . " : Le prenom a-t-il besoin d'être si long ?<br>");
                }
                if(!is_string($participant_data->nom))
                {
                    add_alert_to_ajax_response($participant_type . " : Le nom n'est pas une chaine de caractères <br>");
                    $error = true;
                }
                elseif(count($participant_data->nom)>45)
                {
                    add_alert_to_ajax_response($participant_type . " : Le nom a-t-il besoin d'être si long ?<br>");
                }
            }

            if(isset($participant_data->options))
            {
                if(!is_array($participant_data->options))
                {
                    add_alert_to_ajax_response($participant_type . " : Les options transmises ne le sont pas sous forme de tableau <br>");
                    $error = true;
                }
                elseif(count($participant_data->options)>0)
                {
                    $res = check_participant_options($participant_data, $participant_type, $event_id, $site_id, $promo_id, $error, $left_to_pay);
                    $error = $res['error'];
                    $left_to_pay = $res['left_to_pay'];
                }
            }
            else
            {
                add_alert_to_ajax_response($participant_type . " : Les options du participant ne sont pas transmises <br>");
                $error = true;
            }
            if(!is_numeric($left_to_pay))
            {
                $error = true;
                add_alert_to_ajax_response("Le prix total n'est pas numérique. C'est vraiment étrange, puisqu'il est défini en serveur.");
            }
            elseif($left_to_pay>0.01 && $left_to_pay<-0.01)
            {
                $error = true;
                add_alert_to_ajax_response("Le prix total n'est pas bon.");
            }
            else
            {
                global $total_price;
                if($participant_action=='addition')
                {
                    $total_price+=$participant_data->total_participant_price;
                }
                else
                {
                    $total_price+=$participant_data->participant_price_addition;
                }
            }
        }
    }
    return !$error;
}

function prevent_displaying_on_wrong_ticketing_state($ticketing_state)
{
    global $ajax_json_response;
    if(in_array($ticketing_state, array('coming in some time', 'coming soon', 'ended long ago', 'ended and no reservation')))
    {
        switch ($ticketing_state)
        {
            case 'coming soon':
            case 'coming in some time':
            {
                $message = "La billetterie n'a pas encore commencé.";
                if(isset($ajax_json_response))
                {
                    add_alert_to_ajax_response($message);
                    echo json_encode($ajax_json_response);
                }
                else
                {
                    set_alert_style("Erreur ticketing state");
                    add_alert($message);
                }
                die();
            }
            case 'ended and no reservation':
            case 'ended long ago':
            {
                $message = "La billetterie est finie.";
                if(isset($ajax_json_response))
                {
                    add_alert_to_ajax_response($message);
                    echo json_encode($ajax_json_response);
                }
                else
                {
                    set_alert_style("Erreur ticketing state");
                    add_alert($message);
                }
                die();
            }
        }
    }
}

function check_if_event_should_be_displayed($event,$promo_id, $site_id, $email)
{
    global $ajax_json_response;
    $event_id = $event['event_id'];
    if($event['is_active']==0)
    {
        if(isset($ajax_json_response))
        {
            add_alert_to_ajax_response("L'évènement n'est pas encore actif");
            echo json_encode($ajax_json_response);
        }
        else
        {
            set_alert_style("Erreur évènement non actif");
            add_alert("L'évènement n'est pas encore actif");
        }
        die();
    }

    $icam_has_reservation = participant_has_its_place(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id, "email" => $email));
    $ticketing_state = get_ticketing_state($event, $promo_id, $site_id, $email, $icam_has_reservation);

    prevent_displaying_on_wrong_ticketing_state($ticketing_state);

    return $ticketing_state;
}

function update_reservation_status($status, $pending_reservation)
{
    $update = false;
    $list_purchases = json_decode($pending_reservation['liste_places_options']);
    foreach($list_purchases->participant_ids as $participant_id)
    {
        $should_update = participant_has_pending_event(array("event_id" => $pending_reservation['event_id'], "participant_id" => $participant_id));
        if($should_update)
        {
            $update=true;
            update_participant_status(array("participant_id" => $participant_id, "status" => $status));
        }
    }
    foreach($list_purchases->option_ids as $ids)
    {
        $should_update = participant_has_specific_pending_option(array("event_id" => $pending_reservation['event_id'], "participant_id" => $ids->participant_id, "choice_id" => $ids->choice_id));
        if($should_update)
        {
            $update=true;
            update_option_status(array("participant_id" => $ids->participant_id, "status" => $status, "choice_id" => $ids->choice_id));
        }
    }
    if($update)
    {
        update_transaction_status(array("transaction_id" => $pending_reservation['transaction_id'], "status" => $status));
    }
}

function handle_pending_reservations($login, $event_id)
{
    global $ajax_json_response;
    $pending_reservations = icam_has_pending_reservations(array("login" => $login, "event_id" => $event_id));
    if($pending_reservations !=false)
    {
        if(count($pending_reservations)==1)
        {
            $transaction = get_icam_pending_transaction($login);
            $message = "Vous avez une réservation en attente non payée... " . "<a href='".$transaction['payicam_transaction_url']."' class='btn btn-warning'>Aller la payer</a>";
            if(isset($ajax_json_response))
            {
                add_alert_to_ajax_response($message);
                echo json_encode($ajax_json_response);
            }
            else
            {
                set_alert_style("Erreur transaction attente");
                cancel_or_finish_transaction($transaction['payicam_transaction_url'], $event_id);
            }
            die();
        }
        else
        {
            //Si il y a plus d'une réservation en attente, c'est pas normal, on abandonne les deux.
            foreach($pending_reservations as $pending_reservation)
            {
                update_reservation_status('A', $pending_reservation);
            }
        }
    }
}