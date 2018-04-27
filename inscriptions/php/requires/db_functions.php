<?php

function promo_has_option($ids)
{
    global $db;
    $option_accessibility = $db->prepare('SELECT * FROM promo_site_has_options where event_id = :event_id and option_id = :option_id and promo_id=:promo_id and site_id=:site_id');
    $option_accessibility->execute($ids);
    $option_accessibility = $option_accessibility->fetchAll();
    return count($option_accessibility)==1 ? true : false;
}

function get_all_options($event_id)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id');
    $option_query->execute(array('event_id'=>$event_id));
    return $option_query->fetchAll();
}

function get_promo_specification_details($promo_details)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id and is_removed=0');
    $promos_query->execute($promo_details);
    return current($promos_query->fetchAll());
}

function get_current_select_option_quota($data)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id=pho.choice_id WHERE pho.event_id= :event_id and pho.choice_id= :choice_id and status IN("V", "W")');
    $count_promo->execute($data);
    return $count_promo->fetch()['current_option_quota'];
}
function insert_icam_participant($icam_data)
{
    global $db;
    $icam_insertion = $db->prepare('INSERT INTO participants(status, prenom, nom, is_icam, email, price, telephone, event_id, site_id, promo_id) VALUES ("W", :prenom, :nom, :is_icam, :email, :price, :telephone, :event_id, :site_id, :promo_id)');
    $icam_insertion->execute($icam_data);
    return $db->lastInsertId();
}
function update_icam_participant($icam_data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET telephone=:telephone WHERE participant_id=:icam_id and event_id=:event_id and site_id=:site_id and promo_id=:promo_id');
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
    $icam_insertion = $db->prepare('INSERT INTO participants(status, prenom, nom, is_icam, price, event_id, site_id, promo_id) VALUES ("W", :prenom, :nom, :is_icam, :price, :event_id, :site_id, :promo_id)');
    $icam_insertion->execute($guest_data);
    return $db->lastInsertId();
}
function update_guest_participant($guest_data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participants SET prenom=:prenom, nom=:nom WHERE participant_id=:guest_id and event_id=:event_id and site_id=:site_id and promo_id=:promo_id');
    return $icam_update->execute($guest_data);
}
function get_icam_options_data($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT option_id, option_details FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}
function update_option_status($ids)
{
    global $db;
    $icam_update = $db->prepare('UPDATE participant_has_options SET status=:status WHERE participant_id=:participant_id and choice_id=:choice_id');
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
    $transaction = $db->prepare('INSERT INTO transactions(status, login, liste_places_options, price, payicam_transaction_id, payicam_transaction_url, event_id, icam_id) VALUES ("W", :login, :liste_places_options, :price, :payicam_transaction_id, :payicam_transaction_url, :event_id, :icam_id)');
    $transaction->execute($data);
    return $db->lastInsertId();
}
function update_transaction_status($data)
{
    global $db;
    $icam_update = $db->prepare('UPDATE transactions SET status= :status, date_payement = CURRENT_TIMESTAMP() WHERE transaction_id= :transaction_id');
    return $icam_update->execute($data);
}

function icam_has_pending_reservations($data)
{
    global $db;
    $pending_reservations = $db->prepare('SELECT * FROM transactions WHERE status = "W" and login= :login and event_id= :event_id');
    $pending_reservations->execute($data);
    $pending_reservations = $pending_reservations->fetchAll();
    return empty($pending_reservations) ? false : $pending_reservations;
}
function get_icam_pending_transaction($login)
{
    global $db;
    $pending_reservations = $db->prepare('SELECT * FROM transactions LEFT JOIN events ON events.event_id = transactions.event_id WHERE status = "W" and login= :login');
    $pending_reservations->execute(array("login" => $login));
    return current($pending_reservations->fetchAll());
}

function get_checkbox_option_choice($choice_id)
{
    global $db;
    $option_choices = $db->prepare('SELECT * FROM option_choices oc LEFT JOIN options o ON o.option_id=oc.option_id WHERE choice_id=:choice_id and type="Checkbox"');
    $option_choices->execute(array('choice_id' => $choice_id));
    return $option_choices->fetch();
}
function get_select_option_choice($choice_id)
{
    global $db;
    $option_choices = $db->prepare('SELECT * FROM option_choices oc LEFT JOIN options o ON o.option_id=oc.option_id WHERE choice_id=:choice_id and type="Select"');
    $option_choices->execute(array('choice_id' => $choice_id));
    return $option_choices->fetch();
}
function get_option_article_id($choice_id)
{
    global $db;
    $article_id = $db->prepare('SELECT scoobydoo_article_id FROM option_choices WHERE choice_id=:choice_id');
    $article_id->execute(array('choice_id' => $choice_id));
    return $article_id->fetch()['scoobydoo_article_id'];
}

function get_promo_article_id($ids)
{
    global $db;
    $article_id = $db->prepare('SELECT scoobydoo_article_id FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id');
    $article_id->execute($ids);
    return $article_id->fetch()['scoobydoo_article_id'];
}

function participant_has_pending_event($ids)
{
    global $db;
    $article_id = $db->prepare('SELECT COUNT(*) FROM participants WHERE participant_id=:participant_id and event_id=:event_id and status="W"');
    $article_id->execute($ids);
    return $article_id->fetch()['COUNT(*)']==1 ? true : false;
}
function participant_has_specific_pending_option($ids)
{
    global $db;
    $article_id = $db->prepare('SELECT COUNT(*) FROM participant_has_options WHERE participant_id=:participant_id and event_id=:event_id and choice_id=:choice_id and status="W"');
    $article_id->execute($ids);
    return $article_id->fetch()['COUNT(*)']==1 ? true : false;
}

function icam_id_is_correct($data)
{
    global $db;
    $match = $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id=:event_id and participant_id=:participant_id and status="V" and email=:login and is_icam=1 and promo_id=:promo_id and site_id=:site_id');
    $match->execute($data);
    return $match->fetch()['COUNT(*)']==1 ? true : false;
}

function guest_id_is_correct($data)
{
    global $db;
    $match = $db->prepare('SELECT COUNT(*) FROM participants p LEFT JOIN icam_has_guests ihg ON ihg.guest_id=p.participant_id LEFT JOIN participants pp ON pp.participant_id=ihg.icam_id WHERE p.participant_id=:participant_id and p.event_id=:event_id and p.status="V" and p.is_icam=0 and pp.email=:login and p.promo_id=:promo_id and p.site_id=:site_id and pp.promo_id=:icam_promo_id and pp.site_id=:site_id');
    $match->execute($data);
    return $match->fetch()['COUNT(*)']==1 ? true : false;
}

function get_participant_previous_option_choice_status($data)
{
    global $db;
    $row = $db->prepare('SELECT * FROM participant_has_options WHERE participant_id=:participant_id and choice_id=:choice_id and event_id=:event_id');
    $row->execute($data);
    $row = $row->fetch();
    return empty($row) ? false : $row['status'];
}

function update_participant_option_to_waiting($data)
{
    global $db;
    $option_query = $db->prepare('UPDATE participant_has_options SET status="W", price=:price, option_date=CURRENT_TIMESTAMP(), payement=:payement WHERE event_id=:event_id and participant_id=:participant_id and choice_id=:choice_id');
    return $option_query->execute($data);
}

function get_current_promo_site_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_promo_quota FROM participants WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id and status IN("V", "W")');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_promo_quota'];
}

function get_icam_event_data($identification_data)
{
    global $db;
    $icam_data = $db->prepare('SELECT * FROM participants WHERE email = :email and event_id = :event_id and promo_id = :promo_id and site_id = :site_id and status ="V" ');
    $icam_data->execute($identification_data);
    $icam_data = $icam_data->fetchAll();
    $icam_data = count($icam_data)>1 ? 'several_emails' : current($icam_data);
    return $icam_data;
}

function get_whole_current_quota($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status IN("V", "W")');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
}

function get_participant_option($ids)
{
    global $db;
    if (isset($ids['option_id']) && isset($ids['event_id']))
        $option_query = $db->prepare('
        SELECT pho.*
        FROM participant_has_options pho
        LEFT JOIN option_choices oc ON pho.choice_id = oc.choice_id
        WHERE pho.event_id=:event_id and oc.option_id=:option_id and participant_id=:participant_id and status="V" ');
    elseif (isset($ids['choice_id']))
        $option_query = $db->prepare('SELECT pho.* FROM participant_has_options pho WHERE choice_id=:choice_id and participant_id=:participant_id and status="V" ');
    $option_query->execute($ids);
    return $option_query->fetch();
}

function is_correct_choice_id($ids)
{
    global $db;
    $rows_number = $db->prepare('SELECT COUNT(*) FROM option_choices WHERE choice_id=:choice_id and option_id=:option_id');
    $rows_number->execute($ids);
    return $rows_number->fetch()['COUNT(*)']==1 ? true : false;
}
