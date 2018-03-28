<?php

function get_displayed_participants($event_id, $start_lign, $rows_per_page)
{
    global $db;
    $participant_data = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and status="V" LIMIT :start_lign,:rows_per_page');
    $participant_data->bindParam('start_lign', $start_lign, PDO::PARAM_INT);
    $participant_data->bindParam('rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $participant_data->bindParam('event_id', $event_id, PDO::PARAM_INT);
    $participant_data->execute();
    return $participant_data->fetchAll();
}

function get_participant_options($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id and status="V" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_pending_options($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id and status="W" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_current_guest_number_by_status($participant_id)
{
    global $db;
    $current_guest_number = $db->prepare('SELECT status, COUNT(*) guest_number FROM icam_has_guests INNER JOIN participants ON guest_id = participant_id WHERE icam_id=:participant_id and status="V" GROUP BY status');
    $current_guest_number->execute(array("participant_id" => $participant_id));
    return $current_guest_number->fetchAll();
}

function get_promo_guest_number($ids)
{
    global $db;
    $promo_guest_number = $db->prepare('SELECT guest_number FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id ');
    $promo_guest_number->execute($ids);
    return $promo_guest_number->fetch()['guest_number'];
}

function get_option_name($option_id)
{
    global $db;
    $promo_guest_number = $db->prepare('SELECT name FROM options WHERE option_id=:option_id');
    $promo_guest_number->execute(array("option_id" => $option_id));
    return $promo_guest_number->fetch()['name'];
}