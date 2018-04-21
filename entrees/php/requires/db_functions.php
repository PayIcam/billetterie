<?php

function participant_has_arrived($participant_id)
{
    global $db;
    $is_in = $db->prepare('SELECT * FROM arrivals WHERE participant_id=:participant_id');
    $is_in->execute(array('participant_id' => $participant_id));
    return !empty($is_in->fetchAll());
}

function get_participant_event_arrival_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT p.*, COUNT(a.participant_id) arrival FROM participants p LEFT JOIN arrivals a ON a.participant_id=p.participant_id WHERE p.event_id = :event_id and p.participant_id = :participant_id and status="V" ');
    $participant_data->execute($ids);
    return $participant_data->fetch();
}

function participant_arrives($ids)
{
    global $db;
    $arrival = $db->prepare('INSERT INTO arrivals(participant_id, event_id) VALUES (:participant_id, :event_id)');
    return $arrival->execute($ids);
}

function participant_leaves($ids)
{
    global $db;
    $arrival = $db->prepare('DELETE FROM arrivals WHERE participant_id=:participant_id and event_id=:event_id');
    return $arrival->execute($ids);
}

function get_arrival_number($event_id)
{
    global $db;
    $arrival_number = $db->prepare('SELECT COUNT(*) FROM arrivals WHERE event_id=:event_id');
    $arrival_number->execute(array('event_id' => $event_id));
    return $arrival_number->fetch()['COUNT(*)'];
}