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
function get_promo_name($id)
{
    global $db;
    $name = $db->prepare('SELECT promo_name FROM promos WHERE promo_id=:promo_id');
    $name->execute(array("promo_id" => $id));
    return $name->fetch()['promo_name'];
}
function get_site_name($id)
{
    global $db;
    $name = $db->prepare('SELECT site_name FROM sites WHERE site_id=:site_id');
    $name->execute(array("site_id" => $id));
    return $name->fetch()['site_name'];
}

function get_event_details($event_id)
{
    global $db;
    $event_query = $db->prepare('SELECT * FROM events WHERE event_id=:event_id');
    $event_query->execute(array('event_id'=>$event_id));
    $event = $event_query->fetch();
    return $event;
}
function get_options($event_id)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id');
    $option_query->execute(array('event_id'=>$event_id));
    $options = $option_query->fetchAll();
    return $options;
}