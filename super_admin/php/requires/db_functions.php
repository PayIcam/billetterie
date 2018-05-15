<?php

function update_config_availability($data)
{
    global $db;
    $update = $db->prepare('UPDATE config SET is_active=:is_active WHERE folder=:folder');
    $update->execute($data);
}

function get_config()
{
    global $db;
    $update = $db->query('SELECT * FROM config ORDER BY FIELD(folder, "ticketing", "event_administration", "inscriptions", "participant_administration")');
    return $update->fetchAll();
}