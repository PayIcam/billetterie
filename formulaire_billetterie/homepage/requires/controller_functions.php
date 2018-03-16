<?php

function handle_ticketings_displayed($events_id_accessible)
{
    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $_SESSION['icam_informations']->promo_id;
    $site_id = $_SESSION['icam_informations']->site_id;

    foreach($events_id_accessible as $event_id)
    {
        $event_id = $event_id['event_id'];
        $event = get_event_details($event_id);

        $icam_has_reservation = participant_has_its_place(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id, "email" => $email));
        $ticketing_state = get_ticketing_state($event, $promo_id, $site_id, $email, $icam_has_reservation);

        if(in_array($ticketing_state, array('open', 'coming soon', 'ended not long ago and reservation')))
        {
            display_event($ticketing_state, $event, $icam_has_reservation);
        }
    }
}