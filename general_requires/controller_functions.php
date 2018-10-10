<?php

//Possible qu'il y ait une erreur à cause de Php, qui a des problèmes avec la précision des float...
function is_an_integer($number)
{
    return (floor($number) == $number) && $number>=0;
}

/**
 * Cette fonction permet de déterminer l'état de la billetterie d'un évènement.
 * L'état est renvoyé en chaine de caractères parmi un choix possible
 * @param  [array] $event                [Les informations à propos de l'évènement, obtenues par un SELECT * de la table events]
 * @param  [int or string] $promo_id             [L'id de la promotion de l'utilisateur]
 * @param  [int or string] $site_id              [L'id du site de l'utilisateur]
 * @param  [string] $email                [L'email de l'utilisateur]
 * @param  [boolean] $icam_has_reservation
 * @return [string]                       [in ['open','coming in some time', 'coming soon', 'ended long ago', 'ended and no reservation', 'ended not long ago and reservation']]
 */
function get_ticketing_state($event, $promo_id, $site_id, $email, $icam_has_reservation)
{
    $event_id = $event['event_id'];

    date_default_timezone_set('Europe/Paris');
    $current_datetime = new DateTime();
    $ticketing_start_date = new DateTime($event['ticketing_start_date']);
    $ticketing_end_date = new DateTime($event['ticketing_end_date']);

    if($current_datetime < $ticketing_start_date)
    {
        $interval = $current_datetime->diff($ticketing_start_date);
        if($interval->y > 0 || $interval->m > 0 || $interval->d > 10)
        {
            return 'coming in some time';
        }
        return 'coming soon';
    }
    elseif($current_datetime > $ticketing_end_date)
    {
        $interval = $current_datetime->diff($ticketing_end_date);
        if($interval->y > 0 || $interval->m > 0 || $interval->d > 10)
        {
            return 'ended long ago';
        }
        elseif(!$icam_has_reservation)
        {
            return 'ended and no reservation';
        }
        return 'ended not long ago and reservation';
    }
    return 'open';
}

/**
 * Redirection to homepage if parameter is false
 * @param  [boolean] $is_admin
 */
function redirect_if_not_admin($is_admin)
{
    global $_CONFIG;
    if(!$is_admin)
    {
        header('Location: ' . $_CONFIG['public_url']);
        die();
    }
}

/**
 * Redirection to homepage if user doesn't have rights on the service
 * @return [object] list of all fundations user has rights on
 */
function redirect_if_no_rights()
{
    global $payutcClient, $_CONFIG;
    try
    {
        return $payutcClient->getFundations();
    }
    catch(JsonClient\JsonException $e)
    {
        if($e->gettype() == 'Payutc\Exception\CheckRightException')
        {
            header('Location: '.$_CONFIG['public_url']);
            die();
        }
        else
        {
            set_alert_style("Erreur PayutcJsonClient");
            add_alert("Vous n'avez vraisemblablement pas les droits, mais quelque chose d'innattendu s'est produit. Contactez Grégoire Giraud pour l'aider à résoudre ce bug svp");
            die();
        }
    }
}

/**
 * Checks whether the user has rights on the fundation or not on this service
 * @param  [int]  $fundation_id
 * @param  boolean $death        [if true, then stops the script if user doesn't have rights. Else, add]
 * @return [boolean]                [if $death==false returns true if there is an error, false if not]
 */
function check_user_fundations_rights($fundation_id, $death=true)
{
    global $fundations, $admin_fundations, $error, $ajax_json_response;
    $fundation_ids = array_column($fundations, 'fun_id');
    if(!in_array($fundation_id, $fundation_ids))
    {
        $error_message = "Vous n'avez pas les droits sur cette fondation";
        if($death)
        {
            if(isset($ajax_json_response))
            {
                add_alert_to_ajax_response($error_message);
                echo json_encode($ajax_json_response);
            }
            else
            {
                set_alert_style('Erreur droits de fondation');
                add_alert($error_message);
            }
            die();
        }
        else
        {
            if(isset($ajax_json_response))
            {
                add_alert_to_ajax_response($error_message);
            }
            else
            {
                add_alert($error_message);
            }
            return true;
        }
    }
    elseif($death==false)
    {
        return false;
    }
}

function has_admin_rights($fundation_id, $getPayutcClient)
{
    $payutc_admin = $getPayutcClient('ADMINRIGHT');
    try
    {
        $admin_fundations = $payutc_admin->getFundations();
        $admin_fundation_ids = array_column($admin_fundations, 'fun_id');
        return in_array($fundation_id, $admin_fundation_ids);
    }
    catch(JsonClient\JsonException $e)
    {
        if($e->gettype() == 'Payutc\Exception\CheckRightException')
        {
            return false;
        }
        else
        {
            set_alert_style("Erreur PayutcJsonClient");
            add_alert("Vous n'avez vraisemblablement pas les droits, mais quelque chose d'innattendu s'est produit. Contactez Grégoire Giraud pour l'aider à résoudre ce bug svp");
            die();
        }
    }
}

function check_if_folder_is_active($folder)
{
    global $is_super_admin;
    if(!folder_is_active($folder))
    {
        if(!$is_super_admin)
        {
            set_alert_style('Erreur maintenance');
            add_alert($folder . ' est en maintenance');
            die();
        }
    }
}

/**
 * Cette fonction permet de déterminer si un évènement devrait pouvoir être affiché. On ne veux pas afficher un évènement fermé depuis plus de 6 mois (s'il a été créé depuis plus de 2 mois).
 * @param  [array] $event         [l'évènement en question]
 * @return true si l'évènement ne doit plus être affiché
 */
function event_is_too_old($event)
{
    date_default_timezone_set('Europe/Paris');
    $now = new DateTime();
    $created_on = new DateTime($event['created_on']);
    $ticketing_end_date = new DateTime($event['ticketing_start_date']);

    $creation_to_now_difference = $now->diff($created_on);
    $now_to_ticketing_end_date_difference = $now->diff($ticketing_end_date);

    $created_long_enough_ago = ($creation_to_now_difference->y >= 1 || $creation_to_now_difference->m >= 2);
    $ended_too_long_ago = ($now_to_ticketing_end_date_difference->y >= 1 || $now_to_ticketing_end_date_difference->m >= 6);

    return $created_long_enough_ago && $ended_too_long_ago;
}

/**
 * Cette fonction empèche le fonctionnement normal de la page et affiche un message d'erreur si l'évènement voulu est trop vieux.
 * @param  [array] $event         [l'évènement en question]
 */
function check_if_event_is_not_too_old($event)
{
    global $ajax_json_response;

    if(event_is_too_old($event))
    {
        $message = "L'évènement " . $event['name'] . " est terminé depuis trop longtemps. Il n'est plus possible d'en faire quoi que ce soit. Contactez l'équipe dirigeant PayIcam si vous avez tout de même besoin de retrouver des informations à propos, de le réactiver, ou autres.";
        if(isset($ajax_json_response))
        {
            add_alert_to_ajax_response($message);
            echo json_encode($ajax_json_response);
        }
        else
        {
            set_alert_style("Erreur : Evènement trop ancien");
            add_alert($message);
        }
        die();
    }
}

/**
 * Le but de cette fonction est de séparer les évènements en deux catégories : Ceux qui doivent être affichés, et ceux qui sont trop vieux.
 * Un event est trop vieux si sa date de fin est passée depuis 6 mois et sa date de création passée depuis 2 mois
 * @param  [array] $events [fetchAll des events d'une fondation]
 * @return [array("displayed_events" =>, "not_displayed_events" =>,)]         [description]
 */
function separate_good_bad_events($events) {
    $displayed_events = [];
    $not_displayed_events = [];
    foreach ($events as $event) {
        if(event_is_too_old($event)) {
            array_push($not_displayed_events, $event);
        } else {
            array_push($displayed_events, $event);
        }
    }
    return ["displayed_events" => $displayed_events, "not_displayed_events" => $not_displayed_events];
}