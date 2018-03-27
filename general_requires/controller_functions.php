<?php

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
        header('Location: '.$_CONFIG['public_url']);
        die();
    }
}