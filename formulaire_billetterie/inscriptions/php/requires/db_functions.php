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
    return current($promos_query->fetchAll());
}

function get_current_participants_number($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
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
function get_current_select_option_quota($data)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id and option_details REGEXP :subname');
    $count_promo->execute($data);
    return $count_promo->fetch()['current_option_quota'];
}
function event_id_is_correct($event_id)
{
    if(is_numeric($event_id))
    {
        global $db;
        $correct_id = $db->prepare('SELECT COUNT(*) matches FROM events WHERE event_id= :event_id');
        $correct_id->execute(array("event_id" => $event_id));
        $correct_id = $correct_id->fetch()['matches']==1 ? true : false;
        $correct_id == 1 ? "" : "Cet event_id n'existe même pas";
        return $correct_id;
    }
    elseif($event_id == "no_GET")
    {
        add_error("L'event_id n'est pas spécifié en GET");
        return false;
    }
    else
    {
        add_error("L'event_id spécifiée n'est même pas un entier.");
        return false;
    }
}
function get_icam_event_data($identification_data)
{
    global $db;
    $icam_data = $db->prepare('SELECT participant_id, prenom, nom, is_icam, email, telephone, birthdate, event_id, site_id, promo_id FROM participants WHERE email = :email and event_id = :event_id and promo_id = :promo_id and site_id = :site_id');
    $icam_data->execute($identification_data);
    $icam_data = $icam_data->fetchAll();
    $icam_data = count($icam_data)>1 ? 'several_emails' : current($icam_data);
    return $icam_data;
}
function get_participant_event_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT participant_id, prenom, nom, is_icam, email, telephone, birthdate, event_id, site_id, promo_id FROM participants WHERE event_id = :event_id and participant_id = :participant_id');
    $participant_data->execute($ids);
    return $participant_data->fetch();
}
function insert_icam_participant($icam_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(prenom, nom, is_icam, email, price, telephone, birthdate, event_id, site_id, promo_id) VALUES (:prenom, :nom, :is_icam, :email, :price, :telephone, :birthdate, :event_id, :site_id, :promo_id)');
    return $icam_insertion->execute($icam_data);
}
function update_icam_participant($icam_data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET price=price+:price_addition, telephone=:telephone, birthdate=:birthdate, event_id=:event_id, site_id=:site_id, promo_id=:promo_id WHERE participant_id=:icam_id');
    return $icam_update->execute($icam_data);
}
function insert_guest_participant($guest_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(prenom, nom, is_icam, price, birthdate, event_id, site_id, promo_id) VALUES (:prenom, :nom, :is_icam, :price, :birthdate, :event_id, :site_id, :promo_id)');
    return $icam_insertion->execute($guest_data);
}
function update_guest_participant($guest_data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET price=price+:price_addition, prenom=:prenom, nom=:nom, birthdate=:birthdate, event_id=:event_id, site_id=:site_id, promo_id=:promo_id WHERE participant_id=:guest_id');
    return $icam_update->execute($guest_data);
}
function get_option($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id and option_id=:option_id');
    $option_query->execute($ids);
    return $option_query->fetch();
}
function insert_icams_guest($ids)
{
    global $db;
    $icams_guest = $db->prepare('INSERT INTO icam_has_guests VALUES (:event_id, :icam_id, :guest_id)');
    return $icams_guest->execute($ids);
}
function insert_participant_option($option_data)
{
    global $db;
    $option_query = $db->prepare('INSERT INTO participant_has_options VALUES (:event_id, :participant_id, :option_id, :option_details)');
    return $option_query->execute($option_data);
}
function get_icam_options_data($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT option_id, option_details FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}
function get_participant_option($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT option_id, option_details FROM participant_has_options WHERE event_id=:event_id and option_id=:option_id and participant_id=:participant_id');
    $option_query->execute($ids);
    return $option_query->fetch();
}
function get_icams_guests_ids($ids)
{
    global $db;
    $guests_ids = $db->prepare('SELECT guest_id FROM icam_has_guests WHERE event_id=:event_id and icam_id=:icam_id');
    $guests_ids->execute($ids);
    return $guests_ids->fetchAll();
}