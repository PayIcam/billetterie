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
                echo json_encode($ajax_json_response);
                die();
            }
            else
            {
                set_alert_style("Erreur routing");
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
            echo json_encode($ajax_json_response);
            die();
        }
        else
        {
            set_alert_style("Erreur routing");
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
            echo json_encode($ajax_json_response);
            die();
        }
        else
        {
            set_alert_style("Erreur routing");
            add_error($message);
        }
        return false;
    }
}

function get_specification_details($event_id)
{
    global $db;
    $promos_query = $db->prepare('SELECT * FROM promos_site_specifications WHERE event_id=:event_id and is_removed=0');
    $promos_query->execute(array('event_id'=>$event_id));
    $promos_specifications = $promos_query->fetchAll();
    return $promos_specifications;
}

function get_current_promo_site_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_promo_quota FROM participants WHERE event_id= :event_id and site_id= :site_id and promo_id= :promo_id and status IN("V", "W")');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_promo_quota'];
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
function get_current_options($event_id)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id and is_removed=0');
    $option_query->execute(array('event_id'=>$event_id));
    return $option_query->fetchAll();
}

function get_promo_options($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options o LEFT JOIN promo_site_has_options psho ON o.option_id=psho.option_id WHERE o.event_id=:event_id and is_removed=0 and is_active=1 and promo_id=:promo_id and site_id=:site_id');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_promos_events($ids)
{
    global $db;
    $promos = $db->prepare('SELECT promos_site_specifications.event_id FROM promos_site_specifications JOIN events on events.event_id = promos_site_specifications.event_id WHERE promo_id=:promo_id and site_id=:site_id and events.is_active=1 and promos_site_specifications.is_removed=0');
    $promos->execute($ids);
    return $promos->fetchAll();
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
function participant_has_its_place($identification_data)
{
    global $db;
    $icam_data = $db->prepare('SELECT participant_id, prenom, nom, is_icam, email, telephone, event_id, site_id, promo_id FROM participants WHERE email = :email and event_id = :event_id and promo_id = :promo_id and site_id = :site_id and status = "V" ');
    $icam_data->execute($identification_data);
    $icam_data = $icam_data->fetchAll();
    return !empty($icam_data);
}
function get_participant_event_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and participant_id = :participant_id and status="V" ');
    $participant_data->execute($ids);
    return $participant_data->fetch();
}

function event_has_option($ids)
{
    global $db;
    $option = $db->prepare('SELECT * FROM options WHERE event_id = :event_id and option_id = :option_id and is_removed=0');
    $option->execute($ids);
    $option = $option->fetch();
    return empty($option) ? false : true;
}

function get_whole_current_quota($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status IN("V", "W")');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
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
function insert_icams_guest($ids)
{
    global $db;
    $icams_guest = $db->prepare('INSERT INTO icam_has_guests VALUES (:event_id, :icam_id, :guest_id)');
    return $icams_guest->execute($ids);
}
function get_icams_guests($ids)
{
    global $db;
    $guests = $db->prepare('SELECT * FROM participants WHERE event_id=:event_id and participant_id IN(SELECT guest_id FROM icam_has_guests WHERE icam_id=:icam_id) and status="V"');
    $guests->execute($ids);
    return $guests->fetchAll();
}
function get_option($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id and option_id=:option_id and is_removed=0');
    $option_query->execute($ids);
    return $option_query->fetch();
}
function insert_participant_option($option_data)
{
    global $db;
    $option_query = $db->prepare('INSERT INTO participant_has_options VALUES (:event_id, :participant_id, :option_id, "W", :option_details)');
    return $option_query->execute($option_data);
}
function get_participant_option($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and option_id=:option_id and participant_id=:participant_id and status="V" ');
    $option_query->execute($ids);
    return $option_query->fetch();
}
function get_current_option_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_option_quota FROM participant_has_options WHERE event_id= :event_id and option_id= :option_id and status IN("V", "W")');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_option_quota'];
}
function get_fundation_id($event_id)
{
    global $db;
    $fundation_id = $db->prepare('SELECT fundation_id FROM events WHERE event_id=:event_id');
    $fundation_id->execute(array('event_id' => $event_id));
    return $fundation_id->fetch()['fundation_id'];
}
function get_fundations_events($fundation_id)
{
    global $db;
    $fundation_events = $db->prepare('SELECT * FROM events WHERE fundation_id = :fundation_id');
    $fundation_events->execute(array("fundation_id" => $fundation_id));
    return $fundation_events->fetchAll();
}

function count_current_icam($event_id, $condition=true)
{
    global $db;
    $is_icam = $condition ? 1 : 0;
    $count = $db->prepare('SELECT COUNT(*) FROM participants WHERE is_icam = :is_icam and event_id = :event_id and status="V" ');
    $count->execute(array('event_id' => $event_id, 'is_icam' => $is_icam));
    return $count->fetch()['COUNT(*)'];
}

function count_current_icam_student($event_id, $condition=true)
{
    global $db;
    if($condition)
    {
        $count = $db->prepare('SELECT COUNT(*) FROM participants INNER JOIN promos ON participants.promo_id = promos.promo_id WHERE event_id = :event_id and still_student = 1 and status="V"');
    }
    else
    {
        $count = $db->prepare('SELECT COUNT(*) FROM participants INNER JOIN promos ON participants.promo_id = promos.promo_id WHERE event_id = :event_id and still_student = 0 and status="V"');
    }
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}

function count_current_telephone($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE telephone IS NOT NULL and event_id = :event_id and is_icam=1 and status="V"') : $db->prepare('SELECT COUNT(*) FROM participants WHERE telephone IS NULL and event_id = :event_id and is_icam=1 and status="V"');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_current_bracelet($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE bracelet_identification IS NOT NULL and event_id = :event_id and status="V"') : $db->prepare('SELECT COUNT(*) FROM participants WHERE bracelet_identification IS NULL and event_id = :event_id and status="V"');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_payed($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE price != 0 and event_id = :event_id and status="V"') : $db->prepare('SELECT COUNT(*) FROM participants WHERE price =0 and event_id = :event_id and status="V"');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}

function count_payement($data)
{
    global $db;
    $count = $db->prepare('SELECT COUNT(*) FROM participants WHERE payement=:payement and event_id = :event_id and status="V"');
    $count->execute($data);
    return $count->fetch()['COUNT(*)'];
}
function count_status($data)
{
    global $db;
    $count = $db->prepare('SELECT COUNT(*) FROM participants WHERE status=:status and event_id = :event_id');
    $count->execute($data);
    return $count->fetch()['COUNT(*)'];
}

function count_participants_with_option($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id = :event_id and status="V" and participant_id IN(SELECT participant_id FROM participant_has_options)') : $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id = :event_id and status="V" and participant_id NOT IN(SELECT participant_id FROM participant_has_options)');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function get_option_names($event_id)
{
    global $db;
    $option_name = $db->prepare('SELECT name FROM options WHERE event_id=:event_id');
    $option_name->execute(array("event_id" => $event_id));
    return array_column($option_name->fetchAll(), 'name');
}
function get_event_promo_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT promo_name FROM promos WHERE promo_id IN (SELECT DISTINCT promo_id FROM promos_site_specifications WHERE event_id = :event_id and is_removed=0)');
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}
function get_event_site_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT site_name FROM sites WHERE site_id IN (SELECT DISTINCT site_id FROM promos_site_specifications WHERE event_id = :event_id and is_removed=0)');
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}

function determination_recherche($recherche, $start_lign, $rows_per_page)
{
    global $db, $recherche_bdd;
    $event_id = $_GET['event_id'];

    $promo_names = array_column(get_event_promo_names($event_id), 'promo_name');
    $promo_search_regex = '#(';
    foreach($promo_names as $promo_name)
    {
        $promo_search_regex .= $promo_name . '|';
    }
    $promo_search_regex = substr($promo_search_regex, 0, count($promo_search_regex)-2).'){1}#i';

    $site_names = array_column(get_event_site_names($event_id), 'site_name');
    $site_search_regex = '#(';
    foreach($site_names as $site_name)
    {
        $site_search_regex .= $site_name . '|';
    }
    $site_search_regex = substr($site_search_regex, 0, count($site_search_regex)-2).'){1}#i';

    $promos = get_specification_details($event_id);
    $promo_site_search_regex = '#(';
    foreach($promos as $promo)
    {
        $promo_site_search_regex .= get_promo_name($promo['promo_id']) . ' ' . get_site_name($promo['site_id']) . '|';
    }
    $promo_site_search_regex = substr($promo_site_search_regex, 0, count($promo_site_search_regex)-2).'){1}#i';

    $payements = $db->prepare('SELECT DISTINCT payement FROM participants WHERE event_id=:event_id');
    $payements->execute(array('event_id' => $event_id));
    $payements = array_column($payements->fetchAll(), 'payement');
    $payement_search_regex = '#(';
    foreach($payements as $payement)
    {
        $payement_search_regex .= $payement . '|';
    }
    $payement_search_regex = substr($payement_search_regex, 0, count($payement_search_regex)-2).'){1}#i';

    $options = get_option_names($event_id);
    $option_search_regex = '#(';
    foreach($options as $option)
    {
        $option_search_regex .= $option . '|';
    }
    $option_search_regex = substr($option_search_regex, 0, count($option_search_regex)-2).'){1}#i';

    switch ($recherche)
    {
        case (preg_match("#^0[1-68]([-. ]?[0-9]{2}){4}$#", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and telephone = :telephone and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('telephone', $recherche);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and telephone = :telephone and event_id=:event_id');
            $count_recherche->execute(array('telephone' => $recherche, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($promo_site_search_regex, $recherche) == 1):
            $recherche = explode(' ', $recherche);
            $promo_id = get_promo_id($recherche[0]);
            $site_id = get_site_id($recherche[1]);

            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and promo_id=:promo_id and site_id=:site_id and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('promo_id', $promo_id);
            $recherche_bdd->bindParam('site_id', $site_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and promo_id = :promo_id and site_id=:site_id and event_id=:event_id');
            $count_recherche->execute(array('site_id' => $site_id, 'promo_id' => $promo_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($promo_search_regex, $recherche) == 1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and promo_id = :promo_id and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $promo_id = get_promo_id($recherche);
            $recherche_bdd->bindParam('promo_id', $promo_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and promo_id = :promo_id and event_id=:event_id');
            $count_recherche->execute(array('promo_id' => $promo_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($site_search_regex, $recherche) == 1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and site_id = :site_id and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $site_id = get_site_id($recherche);
            $recherche_bdd->bindParam('site_id', $site_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and site_id = :site_id and event_id=:event_id');
            $count_recherche->execute(array('site_id' => $site_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case(preg_match($option_search_regex, $recherche) ==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants p WHERE event_id = :event_id and status="V" and participant_id IN(SELECT DISTINCT participant_id FROM participant_has_options WHERE option_id=:option_id) ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $option_id = get_option_id($recherche);
            $recherche_bdd->bindParam('option_id', $option_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id = :event_id and status="V" and participant_id IN(SELECT DISTINCT participant_id FROM participant_has_options WHERE option_id=:option_id)');
            $count_recherche->execute(array('event_id' => $event_id, 'option_id' => $option_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case(preg_match("#^[0-9]{1,4}$#", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification = :bracelet_identification and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('bracelet_identification', $recherche);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and bracelet_identification = :bracelet_identification and event_id=:event_id');
            $count_recherche -> execute(array('bracelet_identification' => $recherche, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match("#^icam[s]?$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_icam($event_id);
            break;

        case (preg_match("#^icam[s]? student[s]?$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id and promo_id IN (SELECT promo_id FROM promos WHERE status="V" and still_student=1) ORDER BY participant_id LIMIT :start_lign, :rows_per_page ');
            $count_recherche = count_current_icam_student($event_id);
            break;

        case (preg_match("#^icam[s]? (graduated|diplom[eé][s]?){1}$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id and promo_id IN (SELECT promo_id FROM promos WHERE status="V" and still_student=0) ORDER BY participant_id LIMIT :start_lign, :rows_per_page ');
            $count_recherche = count_current_icam_student($event_id, false);
            break;

        case (preg_match("#^exterieur|extérieur$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 0 and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_icam($event_id, false);
            break;

        case (preg_match("#^telephone$|^téléphone$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and telephone IS NOT NULL and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_telephone($event_id);
            break;

        case (preg_match("#^no[t]? telephone|no[t]? téléphone$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and telephone IS NULL and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_telephone($event_id, false);
            break;

        case (preg_match("#^bracelet$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification IS NOT NULL and event_id = :event_id or bracelet_identification !="" ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_bracelet($event_id);
            break;

        case (preg_match("#^no[t]? bracelet$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification IS NULL and event_id = :event_id or bracelet_identification="" ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_bracelet($event_id, false);
            break;

        case (preg_match("#^payed$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and price !=0 and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_payed($event_id);
            break;

        case (preg_match("#^no[t]? payed$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and price=0 and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_payed($event_id, false);
            break;

        case (preg_match($payement_search_regex, $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and payement=:payement and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('payement', $recherche);
            $count_recherche = count_payement(array('event_id' => $event_id, 'payement' => $recherche));
            break;

        case ('W'):
        case ('A'):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status=:status and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('status', $recherche);
            $count_recherche = count_status(array('event_id' => $event_id, 'status' => $recherche));
            break;

        case (preg_match('#^option$#i', $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and participant_id IN (SELECT participant_id FROM participant_has_options) ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_participants_with_option($event_id);
            break;

        case (preg_match("#^[a-zéèçôîûâ]+ [a-zçôîûâ]+$#i", $recherche)==1):
            $recherche = explode(" " , $recherche);
            $prenom = '^'.$recherche[0];
            $nom = '^'.$recherche[1];

            $recherche_bdd = $db-> prepare('SELECT * FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('prenom', $prenom);
            $recherche_bdd->bindParam('nom', $nom);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id');
            $count_recherche->execute(array('prenom' => $prenom, 'nom' => $nom, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        default:
            $recherche_bdd =$db-> prepare('SELECT * FROM participants WHERE status="V" and event_id = :event_id and (nom REGEXP :recherche OR prenom REGEXP :recherche) ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('recherche', $recherche);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and event_id = :event_id and(nom REGEXP :recherche OR prenom REGEXP :recherche)');
            $count_recherche->execute(array('recherche' => $recherche, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
    }
    $_SESSION['count_recherche'] = $count_recherche;
    $_SESSION['search_match'] = $count_recherche .' entrées correspondent à la recherche';
    $recherche_bdd->bindParam('start_lign', $start_lign, PDO::PARAM_INT);
    $recherche_bdd->bindParam('rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $recherche_bdd->bindParam('event_id', $event_id);
    $recherche_bdd->execute();
    if(!$recherche_bdd)
    {
        print_r($db->errorInfo());
    }
    return $recherche_bdd->fetchAll();
}

function get_icam_inviter_data($guest_id)
{
    global $db;
    $participant_data = $db->prepare('SELECT participants.* FROM participants WHERE participant_id IN (SELECT icam_id from icam_has_guests WHERE guest_id=:guest_id)');
    $participant_data->execute(array("guest_id" => $guest_id));
    return $participant_data->fetch();
}

function get_promo_guest_number($ids)
{
    global $db;
    $promo_guest_number = $db->prepare('SELECT guest_number FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id ');
    $promo_guest_number->execute($ids);
    return $promo_guest_number->fetch()['guest_number'];
}

function get_current_guest_number_by_status($participant_id)
{
    global $db;
    $current_guest_number = $db->prepare('SELECT status, COUNT(*) guest_number FROM icam_has_guests INNER JOIN participants ON guest_id = participant_id WHERE icam_id=:participant_id and status="V" GROUP BY status');
    $current_guest_number->execute(array("participant_id" => $participant_id));
    return $current_guest_number->fetchAll();
}

function get_participant_options($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id and status="V" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_pending_options($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options WHERE event_id=:event_id and participant_id=:participant_id and status="W" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_option_name($option_id)
{
    global $db;
    $option_name = $db->prepare('SELECT name FROM options WHERE option_id=:option_id');
    $option_name->execute(array("option_id" => $option_id));
    return $option_name->fetch()['name'];
}

function get_displayed_participants($event_id, $start_lign, $rows_per_page)
{
    global $db;
    $participant_data = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and status="V" ORDER BY participant_id LIMIT :start_lign,:rows_per_page');
    $participant_data->bindParam('start_lign', $start_lign, PDO::PARAM_INT);
    $participant_data->bindParam('rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $participant_data->bindParam('event_id', $event_id, PDO::PARAM_INT);
    $participant_data->execute();
    return $participant_data->fetchAll();
}

function get_current_participants_number($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status= "V"');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
}