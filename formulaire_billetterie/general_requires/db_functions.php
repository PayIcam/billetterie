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

function event_id_is_correct($event_id)
{
    global $ajax_json_response;
    if(is_numeric($event_id))
    {
        global $db;
        $correct_id = $db->prepare('SELECT COUNT(*) matches FROM events WHERE event_id= :event_id');
        $correct_id->execute(array("event_id" => $event_id));
        $correct_id = $correct_id->fetch()['matches']==1 ? true : false;
        if($correct_id == false)
        {
            $message = "Cet event_id n'existe pas";
            if(isset($ajax_json_response))
            {
                add_error_to_ajax_response($message);
            }
            else
            {
                set_alert_style();
                add_error($message);
            }
        }
        return $correct_id;
    }
    elseif($event_id == "no_GET")
    {
        $message = "L'event_id n'est pas spécifié en GET";
        if(isset($ajax_json_response))
        {
            add_error_to_ajax_response($message);
        }
        else
        {
            set_alert_style();
            add_error($message);
        }
        return false;
    }
    else
    {
        $message = "L'event_id spécifiée n'est même pas un entier.";
        if(isset($ajax_json_response))
        {
            add_error_to_ajax_response($message);
        }
        else
        {
            set_alert_style();
            add_error($message);
        }
        return false;
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
    return $event_query->fetch();
}
function get_all_events_id()
{
    global $db;
    $event_query = $db->prepare('SELECT event_id FROM events');
    $event_query->execute();
    return $event_query->fetchAll();
}
function get_options($event_id)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id');
    $option_query->execute(array('event_id'=>$event_id));
    $options = $option_query->fetchAll();
    return $options;
}

function get_promos_events($ids)
{
    global $db;
    $promos = $db->prepare('SELECT promos_site_specifications.event_id FROM promos_site_specifications JOIN events on events.event_id = promos_site_specifications.event_id WHERE promo_id=:promo_id and site_id=:site_id and events    .is_active=1');
    $promos->execute($ids);
    return $promos->fetchAll();
}

function get_icam_event_data($identification_data)
{
    global $db;
    $icam_data = $db->prepare('SELECT participant_id, prenom, nom, is_icam, email, telephone, birthdate, event_id, site_id, promo_id FROM participants WHERE email = :email and event_id = :event_id and promo_id = :promo_id and site_id = :site_id and status ="V" ');
    $icam_data->execute($identification_data);
    $icam_data = $icam_data->fetchAll();
    $icam_data = count($icam_data)>1 ? 'several_emails' : current($icam_data);
    return $icam_data;
}
function participant_has_its_place($identification_data)
{
    global $db;
    $icam_data = $db->prepare('SELECT participant_id, prenom, nom, is_icam, email, telephone, birthdate, event_id, site_id, promo_id FROM participants WHERE email = :email and event_id = :event_id and promo_id = :promo_id and site_id = :site_id and status = "V" ');
    $icam_data->execute($identification_data);
    $icam_data = $icam_data->fetchAll();
    return !empty($icam_data);
}
function get_participant_event_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and participant_id = :participant_id');
    $participant_data->execute($ids);
    return $participant_data->fetch();
}

function event_has_option($ids)
{
    global $db;
    $option = $db->prepare('SELECT * FROM options WHERE event_id = :event_id and option_id = :option_id');
    $option->execute($ids);
    $option = $option->fetch();
    return empty($option) ? false : true;
}