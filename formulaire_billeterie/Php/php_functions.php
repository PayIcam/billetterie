<?php

function connect_to_db($conf)
{
    try
    {
        $bd = new PDO('mysql:host='.$conf['sql_host'].';dbname='.$conf['sql_db'].';charset=utf8',$conf['sql_user'],$conf['sql_pass'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        $bd ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bd;
    }
    catch(Exeption $e)
    {
        die('erreur:'.$e->getMessage());
    }
}
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
function insert_as_select_option($array_to_insert)
{
    foreach ($array_to_insert as $element)
    {
        echo '<option>'. $element[0] .'</option>';
    }
}
function insert_event_details($table_event_data)
{
    global $db;
    $event_insertion = $db->prepare('INSERT INTO events(name, description, is_active, has_guests, ticketing_start_date, ticketing_end_date, total_quota) VALUES (:name, :description, :is_active, :has_guests, :ticketing_start_date, :ticketing_end_date, :total_quota)');
    return $event_insertion->execute($table_event_data);
}
function insert_specification_details($table_specification_data)
{
    global $db;
    $specification_insertion = $db->prepare('INSERT INTO promos_site_specifications(event_id, site_id, promo_id, price, quota, guest_number) VALUES (:event_id, :site_id, :promo_id, :price, :quota, :guest_number)');
    return $specification_insertion->execute($table_specification_data);
}
function get_promo_id($name)
{
    global $db;
    $id = $db->prepare('SELECT promo_id FROM promos WHERE promo_name=:promo_name');
    $id->execute(array("promo_name" => $name));
    return $id->fetch()['promo_id'];
}
function get_site_id($name)
{
    global $db;
    $id = $db->prepare('SELECT site_id FROM sites WHERE site_name=:site_name');
    $id->execute(array("site_name" => $name));
    return $id->fetch()['site_id'];
}
function get_sites_id()
{
    global $db;
    $id = $db->prepare('SELECT site_id FROM sites');
    $id->execute();
    return $id->fetchAll();
}
function insert_option($table_option_data)
{
    global $db;
    $option_insertion = $db->prepare('INSERT INTO options(name, description, is_active, is_mandatory, type, quota, specifications, event_id) VALUES (:name, :description, :is_active, :is_mandatory, :type, :quota, :specifications, :event_id)');
    return $option_insertion->execute($table_option_data);
}
function insert_option_accessibility($option_accessibility)
{
    global $db;
    $option_accessibility_insertion = $db->prepare('INSERT INTO promo_site_has_options VALUES (:event_id, :site_id, :promo_id, :option_id)');
    return $option_accessibility_insertion->execute($option_accessibility);
}