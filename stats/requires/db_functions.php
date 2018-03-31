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
function get_guests_data($icam_id)
{
    global $db;
    $participant_data = $db->prepare('SELECT participants.* FROM participants WHERE participant_id IN (SELECT guest_id from icam_has_guests WHERE icam_id=:icam_id)');
    $participant_data->execute(array("icam_id" => $icam_id));
    return $participant_data->fetchAll();
}
function get_icam_inviter_data($guest_id)
{
    global $db;
    $participant_data = $db->prepare('SELECT participants.* FROM participants WHERE participant_id IN (SELECT icam_id from icam_has_guests WHERE guest_id=:guest_id)');
    $participant_data->execute(array("guest_id" => $guest_id));
    return $participant_data->fetch();
}

function get_current_promo_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_promo_quota FROM participants WHERE event_id= :event_id and promo_id= :promo_id and status= "V"');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_promo_quota'];
}
function get_current_site_quota($ids)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_site_quota FROM participants WHERE event_id= :event_id and site_id= :site_id and status= "V"');
    $count_promo->execute($ids);
    return $count_promo->fetch()['current_site_quota'];
}

function get_event_promo_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT promo_name FROM promos WHERE promo_id IN (SELECT DISTINCT promo_id FROM promos_site_specifications WHERE event_id = :event_id)');
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}
function get_event_site_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT site_name FROM sites WHERE site_id IN (SELECT DISTINCT site_id FROM promos_site_specifications WHERE event_id = :event_id)');
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}
function get_event_promo_site_names($event_id)
{
    global $db;
    $promos = $db->prepare('SELECT promo_name FROM promos p WHERE promo_id IN (SELECT DISTINCT promo_id FROM promos_site_specifications WHERE event_id = :event_id)')->fetchAll();
    $promos->execute(array('event_id' => $event_id));
    return $promos->fetchAll();
}

function count_current_icam($event_id, $condition=true)
{
    global $db;
    $is_icam = $condition ? 1 : 0;
    $count = $db->prepare('SELECT COUNT(*) FROM participants WHERE is_icam = :is_icam and event_id = :event_id');
    $count->execute(array('event_id' => $event_id, 'is_icam' => $is_icam));
    return $count->fetch()['COUNT(*)'];
}
function count_current_icam_student($event_id, $condition=true)
{
    global $db;
    if($condition)
    {
        $count = $db->prepare('SELECT COUNT(*) FROM participants INNER JOIN promos ON participants.promo_id = promos.promo_id WHERE event_id = :event_id and still_student = 1');
    }
    else
    {
        $count = $db->prepare('SELECT COUNT(*) FROM participants INNER JOIN promos ON participants.promo_id = promos.promo_id WHERE event_id = :event_id and still_student = 0');
    }
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_current_telephone($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE telephone IS NOT NULL and event_id = :event_id and is_icam=1') : $db->prepare('SELECT COUNT(*) FROM participants WHERE telephone IS NULL and event_id = :event_id and is_icam=1');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_current_bracelet($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE bracelet_identification IS NOT NULL and event_id = :event_id') : $db->prepare('SELECT COUNT(*) FROM participants WHERE bracelet_identification IS NULL and event_id = :event_id');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_payed($event_id, $condition=true)
{
    global $db;
    $count = $condition ? $db->prepare('SELECT COUNT(*) FROM participants WHERE price != 0 and event_id = :event_id') : $db->prepare('SELECT COUNT(*) FROM participants WHERE price =0 and event_id = :event_id');
    $count->execute(array('event_id' => $event_id));
    return $count->fetch()['COUNT(*)'];
}
function count_payement($data)
{
    global $db;
    $count = $db->prepare('SELECT COUNT(*) FROM participants WHERE payement=:payement and event_id = :event_id');
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

    switch ($recherche)
    {
        case (preg_match("#^0[1-68]([-. ]?[0-9]{2}){4}$#", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and telephone = :telephone and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('telephone', $recherche);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and telephone = :telephone and event_id=:event_id');
            $count_recherche->execute(array('telephone' => $recherche, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($promo_site_search_regex, $recherche) == 1):
            $recherche = explode(' ', $recherche);
            $promo_id = get_promo_id($recherche[0]);
            $site_id = get_site_id($recherche[1]);

            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and promo_id=:promo_id and site_id=:site_id and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('promo_id', $promo_id);
            $recherche_bdd->bindParam('site_id', $site_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and promo_id = :promo_id and site_id=:site_id and event_id=:event_id');
            $count_recherche->execute(array('site_id' => $site_id, 'promo_id' => $promo_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($promo_search_regex, $recherche) == 1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and promo_id = :promo_id and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $promo_id = get_promo_id($recherche);
            $recherche_bdd->bindParam('promo_id', $promo_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and promo_id = :promo_id and event_id=:event_id');
            $count_recherche->execute(array('promo_id' => $promo_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match($site_search_regex, $recherche) == 1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and site_id = :site_id and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $site_id = get_site_id($recherche);
            $recherche_bdd->bindParam('site_id', $site_id);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and site_id = :site_id and event_id=:event_id');
            $count_recherche->execute(array('site_id' => $site_id, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case(preg_match("#^[0-9]{1,4}$#", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification = :bracelet_identification and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('bracelet_identification', $bracelet_identification);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and bracelet_identification = :bracelet_identification and event_id=:event_id');
            $count_recherche -> execute(array('bracelet_identification' => $recherche, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;

        case (preg_match("#^icam[s]?$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_icam($event_id);
            break;

        case (preg_match("#^icam[s]? student[s]?$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id and promo_id IN (SELECT promo_id FROM promos WHERE status="V" and still_student=1) LIMIT :start_lign, :rows_per_page ');
            $count_recherche = count_current_icam_student($event_id);
            break;

        case (preg_match("#^icam[s]? [graduated|diplom[eé][s]?]$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and event_id = :event_id and promo_id IN (SELECT promo_id FROM promos WHERE status="V" and still_student=0) LIMIT :start_lign, :rows_per_page ');
            $count_recherche = count_current_icam_student($event_id, false);
            break;

        case (preg_match("#^exterieur|extérieur$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 0 and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_icam($event_id, false);
            break;

        case (preg_match("#^telephone$|^téléphone$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and telephone IS NOT NULL and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_telephone($event_id);
            break;

        case (preg_match("#^no[t]? telephone|no[t]? téléphone$#i", $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and is_icam = 1 and telephone IS NULL and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_telephone($event_id, false);
            break;

        case (preg_match("#^bracelet$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification IS NOT NULL and event_id = :event_id or bracelet_identification !="" LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_bracelet($event_id);
            break;

        case (preg_match("#^no[t]? bracelet$#i", $recherche)==1):
            $champ = 'telephone';
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and bracelet_identification IS NULL and event_id = :event_id or bracelet_identification="" LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_current_bracelet($event_id, false);
            break;

        case (preg_match("#^payed$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and price !=0 and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_payed($event_id);
            break;

        case (preg_match("#^no[t]? payed$#i", $recherche)==1):
            $recherche_bdd =$db->prepare('SELECT * FROM participants WHERE status="V" and price=0 and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $count_recherche = count_payed($event_id, false);
            break;

        case (preg_match($payement_search_regex, $recherche)==1):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and payement=:payement and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('payement', $recherche);
            $count_recherche = count_payement(array('event_id' => $event_id, 'payement' => $recherche));
            break;

        case ('W'):
        case ('A'):
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status=:status and event_id = :event_id ORDER BY participant_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('status', $recherche);
            $count_recherche = count_status(array('event_id' => $event_id, 'status' => $recherche));
            break;

        case (preg_match("#^[a-zéèçôîûâ]{1}+[a-zçôîûâ]{1}$#i", $recherche)==1):
            $prenom = '^'.$recherche[0];
            $nom = '^'.$recherche[1];
            $recherche_bdd = $db->prepare('SELECT * FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id LIMIT :start_lign, :rows_per_page');
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

            $recherche_bdd = $db-> prepare('SELECT * FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id LIMIT :start_lign, :rows_per_page');
            $recherche_bdd->bindParam('prenom', $prenom);
            $recherche_bdd->bindParam('nom', $nom);
            $count_recherche = $db->prepare('SELECT COUNT(*) FROM participants WHERE status="V" and nom REGEXP :nom AND prenom REGEXP :prenom and event_id = :event_id');
            $count_recherche->execute(array('prenom' => $prenom, 'nom' => $nom, 'event_id' => $event_id));
            $count_recherche = $count_recherche->fetch()['COUNT(*)'];
            break;


        default:
            $recherche_bdd =$db-> prepare('SELECT * FROM participants WHERE status="V" and event_id = :event_id and (nom REGEXP :recherche OR prenom REGEXP :recherche)  LIMIT :start_lign, :rows_per_page');
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