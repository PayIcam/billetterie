<?php

function handle_ticketings_displayed($events_id_accessible)
{
    var_dump($_SESSION);

    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $_SESSION['icam_informations']->promo_id;
    $site_id = $_SESSION['icam_informations']->site_id;

    foreach($events_id_accessible as $event_id)
    {
        $event_id = $event_id['event_id'];
        $event = get_event_details($event_id);

        date_default_timezone_set('Europe/Paris');
        $current_datetime = new DateTime();
        $ticketing_start_date = new DateTime($event['ticketing_start_date']);
        $ticketing_end_date = new DateTime($event['ticketing_end_date']);

        $ticketing_state = 'open';
        if($current_datetime < $ticketing_start_date)
        {
            $interval = $current_datetime->diff($ticketing_start_date);
            if($interval->y > 0 || $interval->m > 0 || $interval->w > 1)
            {
                break;
            }
            elseif(empty(get_icam_event_data(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id, "email" => $_SESSION['icam_informations']->email))))
            {
                break;
            }
            $ticketing_state = 'waiting';
        }
        elseif($current_datetime > $ticketing_end_date)
        {
            $interval = $current_datetime->diff($ticketing_end_date);
            if($interval->y > 0 || $interval->m > 0 || $interval->w > 2)
            {
                break;
            }
            $ticketing_state = 'passed';
        }
        display_event($ticketing_state, $event);
    }
}