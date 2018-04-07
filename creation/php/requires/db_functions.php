<?php

function get_student_promos()
{
    global $db;
    $promos = $db->query('SELECT promo_name FROM promos WHERE still_student=1');
    $promos = $promos->fetchAll();
    return $promos;
}
function get_graduated_promos()
{
    global $db;
    $promos = $db->query('SELECT promo_name FROM promos WHERE still_student=0');
    $promos = $promos->fetchAll();
    return $promos;
}
function get_sites()
{
    global $db;
    $sites = $db->query('SELECT site_name FROM sites');
    $sites = $sites->fetchAll();
    return $sites;
}
function get_sites_id()
{
    global $db;
    $id = $db->prepare('SELECT site_id FROM sites');
    $id->execute();
    return $id->fetchAll();
}

function insert_event_details($table_event_data)
{
    global $db;
    $event_insertion = $db->prepare('INSERT INTO events(name, description, is_active, ticketing_start_date, ticketing_end_date, total_quota, fundation_id, scoobydoo_category_ids) VALUES (:name, :description, :is_active, :ticketing_start_date, :ticketing_end_date, :total_quota, :fundation_id, :scoobydoo_category_ids)');
    $event_insertion->execute($table_event_data);
    return $db->lastInsertId();
}
function update_event_details($table_event_data)
{
    global $db;
    $event_update = $db->prepare('UPDATE events SET name = :name, description = :description, is_active = :is_active, ticketing_start_date = :ticketing_start_date, ticketing_end_date = :ticketing_end_date, total_quota = :total_quota WHERE event_id = :event_id');
    return $event_update->execute($table_event_data);
}
function get_scoobydoo_event_infos($event_id)
{
    global $db;
    $scoobydoo_ids = $db->prepare('SELECT scoobydoo_category_ids, fundation_id FROM events WHERE event_id = :event_id');
    $scoobydoo_ids->execute($event_id);
    return $scoobydoo_ids->fetch();
}
function get_fundations_events($fundation_id)
{
    global $db;
    $fundation_events = $db->prepare('SELECT * FROM events WHERE fundation_id = :fundation_id');
    $fundation_events->execute(array("fundation_id" => $fundation_id));
    return $fundation_events->fetchAll();
}
function insert_specification_details($table_specification_data)
{
    global $db;
    $specification_insertion = $db->prepare('INSERT INTO promos_site_specifications(event_id, site_id, promo_id, price, quota, guest_number, scoobydoo_article_id) VALUES (:event_id, :site_id, :promo_id, :price, :quota, :guest_number, :scoobydoo_article_id)');
    return $specification_insertion->execute($table_specification_data);
}
function update_specification_details($table_specification_data)
{
    global $db;
    $specification_insertion = $db->prepare('UPDATE promos_site_specifications SET price=:price, quota=:quota, guest_number=:guest_number, is_removed=0 WHERE event_id = :event_id and promo_id = :promo_id and site_id = :site_id');
    return $specification_insertion->execute($table_specification_data);
}
function delete_specification_details($ids)
{
    global $db;
    $specification_deletion = $db->prepare('DELETE FROM promos_site_specifications WHERE event_id = :event_id and promo_id=:promo_id and site_id=:site_id');
    return $specification_deletion->execute($ids);
}

function get_option_ids_from_event($event_id)
{
    global $db;
    $option_ids = $db->prepare('SELECT option_id FROM options WHERE event_id = :event_id and is_removed=0');
    $option_ids->execute(array("event_id"=>$event_id));
    return $option_ids->fetchAll();
}
function insert_option($table_option_data)
{
    global $db;
    $option_insertion = $db->prepare('INSERT INTO options(name, description, is_active, is_mandatory, type, quota, specifications, event_id) VALUES (:name, :description, :is_active, :is_mandatory, :type, :quota, :specifications, :event_id)');
    $option_insertion->execute($table_option_data);
    return $db->lastInsertId();
}
function update_option($table_option_data)
{
    global $db;
    $option_update = $db->prepare('UPDATE options SET name= :name, description= :description, is_active= :is_active, is_mandatory= :is_mandatory, type= :type, quota= :quota, specifications= :specifications, event_id= :event_id WHERE event_id= :event_id and option_id= :option_id and is_removed=0');
    return $option_update->execute($table_option_data);
}
function delete_option($ids)
{
    global $db;
    $option_deletion = $db->prepare('DELETE FROM options WHERE event_id= :event_id and option_id= :option_id');
    return $option_deletion->execute($ids);
}

function get_option_accessibility($ids)
{
    global $db;
    $option_accessibility = $db->prepare('SELECT * FROM promo_site_has_options where event_id = :event_id and option_id = :option_id');
    $option_accessibility->execute($ids);
    return $option_accessibility->fetchAll();
}
function insert_option_accessibility($option_accessibility)
{
    global $db;
    $option_accessibility_insertion = $db->prepare('INSERT INTO promo_site_has_options VALUES (:event_id, :site_id, :promo_id, :option_id)');
    return $option_accessibility_insertion->execute($option_accessibility);
}
function delete_previous_option_accessibility($event_id)
{
    global $db;
    $option_accessibility_deletion = $db->prepare('DELETE FROM promo_site_has_options WHERE event_id = :event_id');
    return $option_accessibility_deletion->execute(array("event_id" => $event_id));
}
function can_delete_promo($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) rows FROM participants WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id');
    $count_promo->execute($ids);
    return $count_promo->fetch()['rows'] == 0 ? true : false;
}
function remove_promo($ids)
{
    global $db;
    $promo_removal = $db->prepare('UPDATE promos_site_specifications SET is_removed = 1 WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id');
    return $promo_removal->execute($ids);
}
function can_delete_option($ids)
{
    global $db;
    $count_option = $db->prepare('SELECT COUNT(*) rows FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id');
    $count_option->execute($ids);
    return $count_option->fetch()['rows'] == 0 ? true : false;
}
function remove_option($ids)
{
    global $db;
    $option_removal = $db->prepare('UPDATE options SET is_removed = 1 WHERE event_id= :event_id and option_id= :option_id');
    return $option_removal->execute($ids);
}

function get_removed_specification_details($event_id)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id and is_removed=1');
    $promos_query->execute(array('event_id'=>$event_id));
    $promos_specifications = $promos_query->fetchAll();
    return $promos_specifications;
}
function get_all_specification_details($event_id)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id');
    $promos_query->execute(array('event_id'=>$event_id));
    $promos_specifications = $promos_query->fetchAll();
    return $promos_specifications;
}
function a_participant_would_have_to_pay_obliged_option($ids)
{
    global $db;
    $count_option = $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id=:event_id and promo_id IN(SELECT promo_id FROM promo_site_has_options WHERE option_id=:option_id) and site_id IN(SELECT site_id FROM promo_site_has_options WHERE option_id=:option_id) and participant_id NOT IN(SELECT participant_id FROM participant_has_options WHERE option_id=:option_id and status="V")');
    $count_option->execute($ids);
    return $count_option->fetch()['COUNT(*)'] == 0 ? false : true;
}