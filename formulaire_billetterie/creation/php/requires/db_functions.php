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
    $event_insertion = $db->prepare('INSERT INTO events(name, description, is_active, has_guests, ticketing_start_date, ticketing_end_date, total_quota) VALUES (:name, :description, :is_active, :has_guests, :ticketing_start_date, :ticketing_end_date, :total_quota)');
    return $event_insertion->execute($table_event_data);
}
function update_event_details($table_event_data)
{
    global $db;
    $event_update = $db->prepare('UPDATE events SET name = :name, description = :description, is_active = :is_active, has_guests = :has_guests, ticketing_start_date = :ticketing_start_date, ticketing_end_date = :ticketing_end_date, total_quota = :total_quota WHERE event_id = :event_id');
    return $event_update->execute($table_event_data);
}

function get_specification_details($event_id)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id');
    $promos_query->execute(array('event_id'=>$event_id));
    $promos_specifications = $promos_query->fetchAll();
    return $promos_specifications;
}
function insert_specification_details($table_specification_data)
{
    global $db;
    $specification_insertion = $db->prepare('INSERT INTO promos_site_specifications(event_id, site_id, promo_id, price, quota, guest_number) VALUES (:event_id, :site_id, :promo_id, :price, :quota, :guest_number)');
    return $specification_insertion->execute($table_specification_data);
}
function delete_specification_details($event_id)
{
    global $db;
    $specification_deletion = $db->prepare('DELETE FROM promos_site_specifications WHERE event_id = :event_id');
    return $specification_deletion->execute(array("event_id" => $event_id));
}

function get_option_ids_from_event($event_id)
{
    global $db;
    $option_ids = $db->prepare('SELECT option_id FROM options where event_id = :event_id');
    $option_ids->execute(array("event_id"=>$event_id));
    return $option_ids->fetchAll();
}
function insert_option($table_option_data)
{
    global $db;
    $option_insertion = $db->prepare('INSERT INTO options(name, description, is_active, is_mandatory, type, quota, specifications, event_id) VALUES (:name, :description, :is_active, :is_mandatory, :type, :quota, :specifications, :event_id)');
    return $option_insertion->execute($table_option_data);
}
function update_option($table_option_data)
{
    global $db;
    $option_update = $db->prepare('UPDATE options SET name= :name, description= :description, is_active= :is_active, is_mandatory= :is_mandatory, type= :type, quota= :quota, specifications= :specifications, event_id= :event_id WHERE event_id= :event_id and option_id= :option_id');
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
function delete_previous_option_accessibility($id)
{
    global $db;
    $option_accessibility_deletion = $db->prepare('DELETE FROM promo_site_has_options WHERE event_id = :event_id');
    return $option_accessibility_deletion->execute($id);
}