<?php

function prepare_participant_displaying($participant)
{
    global $event_id;

    $participant['promo'] = get_promo_name($participant['promo_id']);
    $participant['site'] = get_site_name($participant['site_id']);

    $participant['promo_guest_number'] = get_promo_guest_number(array("event_id" => $event_id, "promo_id" => $participant['promo_id'], "site_id" => $participant['site_id']));
    $guest_numbers_by_status = get_current_guest_number_by_status($participant['participant_id']);

    $participant['validated_guest_number'] = 0;
    $participant['waiting_guest_number'] = 0;
    $participant['cancelled_guest_number'] = 0;
    foreach($guest_numbers_by_status as $guest)
    {
        switch($guest['status'])
        {
            case 'V':
                $participant['validated_guest_number'] = $guest['guest_number'];
                break;
            case 'W':
                $participant['waiting_guest_number'] = $guest['guest_number'];
                break;
            case 'A':
                $participant['cancelled_guest_number'] = $guest['guest_number'];
                break;
        }
    }
    $participant['current_promo_guest_number'] = $participant['validated_guest_number'] . "/" . $participant['promo_guest_number'];

    $participant['validated_options'] = get_participant_options(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));
    $participant['pending_options'] = get_pending_options(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));

    return $participant;
}