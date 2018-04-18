<?php

function participant_has_arrived($participant_id)
{
    global $db;
    $is_in = $db->prepare('SELECT * FROM arrivals WHERE participant_id=:participant_id');
    $is_in->execute(array('participant_id' => $participant_id));
    return !empty($is_in->fetchAll());
}