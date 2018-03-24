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
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status= "V"');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
}
function get_current_promo_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_promo_quota FROM participants WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id and status= "V"');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_promo_quota'];
}
function get_current_option_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id and status= "V"');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_option_quota'];
}
function get_current_select_option_quota($data)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id and option_details REGEXP :subname and status= "V"');
    $count_promo->execute($data);
    return $count_promo->fetch()['current_option_quota'];
}
function insert_icam_participant($icam_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(status, prenom, nom, is_icam, email, price, telephone, birthdate, event_id, site_id, promo_id) VALUES ("W", :prenom, :nom, :is_icam, :email, :price, :telephone, :birthdate, :event_id, :site_id, :promo_id)');
    $icam_insertion->execute($icam_data);
    return $db->lastInsertId();
}
function update_icam_participant($icam_data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET price=price+:price_addition, telephone=:telephone, birthdate=:birthdate, event_id=:event_id, site_id=:site_id, promo_id=:promo_id WHERE participant_id=:icam_id');
    return $icam_update->execute($icam_data);
}
function update_participant_status($data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET status= :status WHERE participant_id= :participant_id');
    return $icam_update->execute($data);
}
function insert_guest_participant($guest_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(status, prenom, nom, is_icam, price, birthdate, event_id, site_id, promo_id) VALUES ("W", :prenom, :nom, :is_icam, :price, :birthdate, :event_id, :site_id, :promo_id)');
    $icam_insertion->execute($guest_data);
    return $db->lastInsertId();
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
    $option_query = $db->prepare('INSERT INTO participant_has_options VALUES (:event_id, :participant_id, :option_id, "W", :option_details)');
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
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and option_id=:option_id and participant_id=:participant_id');
    $option_query->execute($ids);
    return $option_query->fetch();
}
function update_option_status($ids)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participant_has_options SET status= :status WHERE participant_id=:participant_id and option_id=:option_id');
    return $icam_update->execute($ids);
}
function get_icams_guests_ids($ids)
{
    global $db;
    $guests_ids = $db->prepare('SELECT guest_id FROM icam_has_guests WHERE event_id=:event_id and icam_id=:icam_id');
    $guests_ids->execute($ids);
    return $guests_ids->fetchAll();
}
function insert_transaction($data)
{
    global $db;
    $transaction = $db->prepare('INSERT INTO transactions(status, login, liste_places_options, price, payicam_transaction_id, payicam_transaction_url, event_id) VALUES ("W", :login, :liste_places_options, :price, :payicam_transaction_id, :payicam_transaction_url, :event_id)');
    $transaction->execute($data);
    return $db->lastInsertId();
}
function update_transaction_status($data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE transactions SET status= :status, date_payement = CURRENT_TIMESTAMP() WHERE transaction_id= :transaction_id');
    return $icam_update->execute($data);
}

function get_pending_reservations($event_id=false, $login=false)
{
    global $db;
    if($login==false && $event_id==false)
    {
        $pending_reservations = $db->query('SELECT * FROM transactions INNER JOIN events ON events.event_id = transactions.event_id WHERE status = "W"');
        return $pending_reservations->fetchAll();
    }
    elseif($event_id==false)
    {
        $pending_reservations = $db->prepare('SELECT * FROM transactions INNER JOIN events ON events.event_id = transactions.event_id WHERE status = "W" and login= :login');
        $pending_reservations->execute(array("login" => $login));
        return $pending_reservations->fetchAll();
    }
    elseif($login==false)
    {
        $pending_reservations = $db->prepare('SELECT * FROM transactions INNER JOIN events ON events.event_id = transactions.event_id WHERE status = "W" and transactions.event_id= :event_id');
        $pending_reservations->execute(array("event_id" => $event_id));
        return $pending_reservations->fetchAll();
    }
    else
    {
        $pending_reservations = $db->prepare('SELECT * FROM transactions INNER JOIN events ON events.event_id = transactions.event_id WHERE status = "W" and login= :login and transactions.event_id= :event_id');
        $pending_reservations->execute(array("login" => $login, "event_id" => $event_id));
        return $pending_reservations->fetchAll();
    }
}
function get_icam_pending_transaction($login)
{
    global $db;
    $pending_reservations = $db->prepare('SELECT * FROM transactions INNER JOIN events ON events.event_id = transactions.event_id WHERE status = "W" and login= :login');
    $pending_reservations->execute(array("login" => $login));
    return current($pending_reservations->fetchAll());
}
function icam_has_pending_reservations($data)
{
    global $db;
    $pending_reservations = $db->prepare('SELECT * FROM transactions WHERE status = "W" and login= :login and event_id= :event_id');
    $pending_reservations->execute($data);
    $pending_reservations = $pending_reservations->fetchAll();
    return empty($pending_reservations) ? false : $pending_reservations;
}
