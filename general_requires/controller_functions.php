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
 */
function redirect_if_no_rights()
{
    global $payutcClient, $_CONFIG;
    try
    {
        $fundations = $payutcClient->getFundations();
        return $fundations;
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
    global $fundations, $error, $ajax_json_response;
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