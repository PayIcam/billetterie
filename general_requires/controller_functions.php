<?php

//Possible qu'il y ait une erreur à cause de Php, qui a des problèmes avec la précision des float............
function is_an_integer($number)
{
    return (floor($number) == $number) && $number>=0;
}

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

function redirect_if_not_admin($is_admin)
{
    global $_CONFIG;
    if(!$is_admin)
    {
        header('Location: ' . $_CONFIG['public_url']);
        die();
    }
}

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

function check_user_fundations_rights($fundation_id, $death=true)
{
    global $fundations, $error, $ajax_json_response;
    $fundation_ids = array_column($fundations, 'fun_id');
    if(!in_array($fundation_id, $fundation_ids))
    {
        $error_message = "Vous n'avez pas les droits sur cette fondation";
        if($death)
        {
            set_alert_style('Erreur droits de fondation');
            if(isset($ajax_json_response))
            {
                add_alert_to_ajax_response($error_message);
            }
            else
            {
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
            $error = true;
        }
    }
}