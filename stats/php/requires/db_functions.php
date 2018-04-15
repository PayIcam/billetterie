<?php

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

function get_current_guest_number_by_status($participant_id)
{
    global $db;
    $current_guest_number = $db->prepare('SELECT status, COUNT(*) guest_number FROM icam_has_guests INNER JOIN participants ON guest_id = participant_id WHERE icam_id=:participant_id and status="V" GROUP BY status');
    $current_guest_number->execute(array("participant_id" => $participant_id));
    return $current_guest_number->fetchAll();
}

function get_current_participants_number($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status= "V"');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'];
}

function get_promo_guest_number($ids)
{
    global $db;
    $promo_guest_number = $db->prepare('SELECT guest_number FROM promos_site_specifications WHERE event_id=:event_id and promo_id=:promo_id and site_id=:site_id ');
    $promo_guest_number->execute($ids);
    return $promo_guest_number->fetch()['guest_number'];
}

function get_option_name($option_id)
{
    global $db;
    $option_name = $db->prepare('SELECT name FROM options WHERE option_id=:option_id');
    $option_name->execute(array("option_id" => $option_id));
    return $option_name->fetch()['name'];
}
function get_option_names($event_id)
{
    global $db;
    $option_name = $db->prepare('SELECT name FROM options WHERE event_id=:event_id');
    $option_name->execute(array("event_id" => $event_id));
    return array_column($option_name->fetchAll(), 'name');
}
function get_option_id($option_name)
{
    global $db;
    $option_id = $db->prepare('SELECT option_id FROM options WHERE name=:name');
    $option_id->execute(array("name" => $option_name));
    return $option_id->fetch()['option_id'];
}

function get_icam_inviter_data($guest_id)
{
    global $db;
    $participant_data = $db->prepare('SELECT participants.* FROM participants WHERE participant_id IN (SELECT icam_id from icam_has_guests WHERE guest_id=:guest_id)');
    $participant_data->execute(array("guest_id" => $guest_id));
    return $participant_data->fetch();
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
function get_event_promo_site_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT promo_name FROM promos p WHERE promo_id IN (SELECT DISTINCT promo_id FROM promos_site_specifications WHERE event_id = :event_id and is_removed=0)');
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}

function get_promo_status($promo_name)
{
    global $db;
    $promos = $db->prepare('SELECT still_student FROM promos WHERE promo_name=:promo_name');
    $promos->execute(array('promo_name' => $promo_name));
    return $promos->fetch()['still_student'];
}

function get_icams_guests_info($ids)
{
    global $db;
    $guests_ids = $db->prepare('SELECT * FROM participants WHERE participant_id in (SELECT guest_id FROM icam_has_guests WHERE event_id=:event_id and icam_id=:icam_id) and status="V"');
    $guests_ids->execute($ids);
    return $guests_ids->fetchAll();
}
function get_guests_icam_inviter_info($ids)
{
    global $db;
    $guests_ids = $db->prepare('SELECT * FROM participants WHERE participant_id in (SELECT icam_id FROM icam_has_guests WHERE event_id=:event_id and guest_id=:guest_id)');
    $guests_ids->execute($ids);
    return $guests_ids->fetch();
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
function bracelet_identification_is_available($data)
{
    global $db;
    if(isset($data['participant_id']))
    {
        $count = $db->prepare('SELECT * FROM participants WHERE status="V" and event_id = :event_id and bracelet_identification=:bracelet_identification and participant_id != :participant_id');
    }
    else
    {
        $count = $db->prepare('SELECT * FROM participants WHERE status="V" and event_id = :event_id and bracelet_identification=:bracelet_identification');
    }
    $count->execute($data);
    return empty($count->fetch());
}
function update_participant_data($data)
{
    global $db;
    if(count($data)==5)
    {
        $update = $db->prepare('UPDATE participants SET bracelet_identification=:bracelet_identification, nom=:nom, prenom=:prenom WHERE event_id=:event_id and participant_id=:participant_id');
        $update->execute($data);
    }
    elseif(count($data==3))
    {
        $update = $db->prepare('UPDATE participants SET bracelet_identification=:bracelet_identification WHERE event_id=:event_id and participant_id=:participant_id');
        $update->execute($data);
    }
}
function count_participants_with_option($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id = :event_id and status="V" and participant_id IN(SELECT participant_id FROM participant_has_options)') : $db->prepare('SELECT COUNT(*) FROM participants WHERE event_id = :event_id and status="V" and participant_id NOT IN(SELECT participant_id FROM participant_has_options)');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
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

        case (preg_match("#^[a-zéèçôîûâ]{1}+[a-zçôîûâ]{1}$#i", $recherche)==1):
            $prenom = '^'.$recherche[0];
            $nom = '^'.$recherche[1];
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('prenom', $prenom);
            $recherche_bdd->bindParam('nom', $nom);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id');
            $count_recherche->execute(array('prenom' => $prenom, 'nom' => $nom, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
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

function get_participant_promo_site_ids($ids)
{
    global $db;
    $promo_site_ids = $db->prepare('SELECT promo_id, site_id FROM participants WHERE event_id =:event_id and participant_id =:participant_id');
    $promo_site_ids->execute($ids);
    return $promo_site_ids->fetch();
}

function add_participant($participant_data)
{
    global $db;
    $addition = $db->prepare('INSERT INTO participants(prenom, nom, status, is_icam, price, payement, email, telephone, bracelet_identification, event_id, site_id, promo_id) VALUES (:prenom, :nom, :status, :is_icam, :price, :payement, :email, :telephone, :bracelet_identification, :event_id, :site_id, :promo_id)');
    $addition->execute($participant_data);
    return $db->lastInsertId();
}
function add_participant_option($option_data)
{
    global $db;
    $option_query = $db->prepare('INSERT INTO participant_has_options VALUES (:event_id, :participant_id, :option_id, "V", :option_details)');
    return $option_query->execute($option_data);
}
function get_select_mandatory_options($ids)
{
    global $db;
    $select_mandatory_options = $db->prepare('SELECT * FROM options o LEFT JOIN promo_site_has_options psho ON o.option_id = psho.option_id WHERE is_active=1 and is_removed=0 and type="Select" and is_mandatory=1 and o.event_id=:event_id and promo_id=:promo_id and site_id=:site_id');
    $select_mandatory_options->execute($ids);
    return $select_mandatory_options->fetchAll();
}
function get_optional_options($ids)
{
    global $db;
    $optional_options = $db->prepare('SELECT * FROM options o LEFT JOIN promo_site_has_options psho ON o.option_id = psho.option_id WHERE (is_active=1 and is_removed=0 and o.event_id=:event_id and promo_id=:promo_id and site_id=:site_id and o.option_id NOT IN(SELECT option_id FROM participant_has_options WHERE participant_id=:participant_id)) and ((type="Select" and is_mandatory=0) or (type="Checkbox"))');
    $optional_options->execute($ids);
    return $optional_options->fetchAll();
}
function option_can_be_added($ids)
{
    global $db;
    $optional_options = $db->prepare('SELECT COUNT(*) matches FROM options o LEFT JOIN promo_site_has_options psho ON o.option_id = psho.option_id WHERE (o.option_id=:option_id and is_removed = 0 and is_active = 1 and promo_id=:promo_id and site_id=:site_id and o.event_id=:event_id) and ((type="Select" and is_mandatory=0) or (type="Checkbox"))');
    $optional_options->execute($ids);
    return $optional_options->fetch()['matches'] == 1 ? true : false;
}
function participant_has_option($ids)
{
    global $db;
    $optional_options = $db->prepare('SELECT COUNT(*) matches FROM participant_has_options WHERE participant_id=:participant_id and option_id=:option_id and event_id=:event_id');
    $optional_options->execute($ids);
    return $optional_options->fetch()['matches'] == 1 ? true : false;
}
function get_event_details_stats($event_id)
{
    global $db;
    $details_stats = $db->prepare('
        SELECT e.*, SUM(IF(p.status IN ("V", "W"), 1, 0)) total_count, SUM(IF(p.bracelet_identification IS NULL or p.status="A", 0, 1)) total_bracelet_count, SUM(IF(pr.still_student = 1 and p.status!="A", 1, 0)) student_count, SUM(IF(pr.still_student = 0 and p.status!="A", 1, 0)) graduated_count, SUM(IF(pr.promo_name="Invités" and p.status!="A", 1, 0)) guests_count
        FROM events e
        LEFT JOIN participants p on p.event_id=e.event_id LEFT JOIN promos pr ON pr.promo_id=p.promo_id LEFT JOIN promos_site_specifications pss ON p.promo_id = pss.promo_id and p.site_id = pss.site_id
        WHERE e.event_id=:event_id');
    $details_stats->execute(array('event_id' => $event_id));

    $type_quotas = $db->prepare('SELECT SUM(IF(pr.still_student=0, quota, 0)) graduated_quota, SUM(IF(pr.still_student=1, quota, 0)) student_quota, SUM(IF(pr.promo_name="Invités", quota, 0)) guest_quota FROM promos_site_specifications pss LEFT JOIN promos pr ON pr.promo_id=pss.promo_id WHERE pss.event_id=:event_id and (pr.still_student!=2 or pr.promo_name="Invités")');
    $type_quotas->execute(array('event_id' => $event_id));
    return array_merge($details_stats->fetch(), $type_quotas->fetch());
}
function get_promo_specification_details_stats($event_id)
{
    global $db;
    $details_stats = $db->prepare('
        SELECT pr.promo_name, s.site_name, pss.quota, pss.guest_number, SUM(IF(p.status IN ("V", "W"), 1, 0)) promo_count, SUM(IF(p.bracelet_identification IS NULL or p.status="A", 0, 1)) bracelet_count
        FROM promos_site_specifications pss
        LEFT JOIN participants p ON p.promo_id = pss.promo_id and p.site_id = pss.site_id LEFT JOIN promos pr ON pr.promo_id=pss.promo_id LEFT JOIN sites s ON s.site_id=pss.site_id
        WHERE p.event_id=:event_id and status != "A"
        GROUP BY pss.promo_id, pss.site_id, pss.quota, pss.guest_number');
    $details_stats->execute(array('event_id' => $event_id));
    $details_stats = $details_stats->fetchAll();
    $guest_promo_count = $db->prepare('SELECT COUNT(ihg.icam_id) invited_guests
        FROM promos_site_specifications pss
        LEFT JOIN participants p ON p.promo_id = pss.promo_id and p.site_id = pss.site_id LEFT JOIN icam_has_guests ihg on ihg.icam_id=p.participant_id
        WHERE p.event_id=:event_id and status != "A"
        GROUP BY p.promo_id, p.site_id');
    $guest_promo_count->execute(array('event_id' => $event_id));
    $guest_promo_count = $guest_promo_count->fetchAll();
    for($i=0; $i<count($guest_promo_count); $i++)
    {
        $combined_array[] = array_merge($details_stats[$i], $guest_promo_count[$i]);
    }
    return $combined_array;
}
function get_event_days_stats($event_id)
{
    global $db;
    $details_stats = $db->prepare('SELECT DATE(p.inscription_date) day, COUNT(p.participant_id) nombre FROM events e LEFT JOIN participants p ON p.event_id = e.event_id WHERE e.event_id=:event_id GROUP BY DATE(p.inscription_date) ORDER BY day DESC LIMIT 0,15');
    $details_stats->execute(array('event_id' => $event_id));
    return $details_stats->fetchAll();
}
function get_event_payments_stats($event_id)
{
    global $db;
    $payment_stats = $db->prepare('SELECT payement, COUNT(*) nombre FROM participants p WHERE event_id=:event_id GROUP BY payement');
    $payment_stats->execute(array('event_id' => $event_id));
    return $payment_stats->fetchAll();
}