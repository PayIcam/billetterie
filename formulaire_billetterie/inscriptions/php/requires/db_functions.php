<?php

function promo_has_option($ids)
{
    global $db;
    $option_accessibility = $db->prepare('SELECT * FROM promo_site_has_options where event_id = :event_id and option_id = :option_id and promo_id=:promo_id and site_id=:site_id');
    $option_accessibility->execute($ids);
    $option_accessibility = $option_accessibility->fetchAll();
    return count($option_accessibility)==1 ? true : false;
}
function get_promo_specification_details($promo_details)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id');
    $promos_query->execute($promo_details);
    return $promos_query->fetchAll()[0];
}

function get_current_participants_number($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id');
    $count_promo->execute(array("event_id" => $event_id));
    return count($count_promo->fetch()['current_total_quota']);
}
function get_current_promo_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_promo_quota FROM participants WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_promo_quota'];
}
function get_current_option_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_option_quota'];
}
function event_id_is_correct($event_id)
{
    if(is_numeric($event_id))
    {
        global $db;
        $correct_id = $db->prepare('SELECT COUNT(*) matches FROM events WHERE event_id= :event_id');
        $correct_id->execute(array("event_id" => $event_id));
        return $correct_id->fetch()['matches']==1 ? true : false;
    }
    else
    {
        return false;
    }
}
function insert_icam_participant($icam_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(prenom, nom, is_icam, email, price, telephone, birthdate, event_id, site_id, promo_id) VALUES (:prenom, :nom, :is_icam, :email, :price, :telephone, :birthdate, :event_id, :site_id, :promo_id)');
    return $icam_insertion->execute($icam_data);
}
function insert_guest_participant($icam_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(prenom, nom, is_icam, price, birthdate, event_id, site_id, promo_id) VALUES (:prenom, :nom, :is_icam, :price, :birthdate, :event_id, :site_id, :promo_id)');
    return $icam_insertion->execute($icam_data);
}