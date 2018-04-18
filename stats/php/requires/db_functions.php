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

function get_option_name($option_id)
{
    global $db;
    $option_name = $db->prepare('SELECT name FROM options WHERE option_id=:option_id');
    $option_name->execute(array("option_id" => $option_id));
    return $option_name->fetch()['name'];
}
function get_option_id($option_name)
{
    global $db;
    $option_id = $db->prepare('SELECT option_id FROM options WHERE name=:name');
    $option_id->execute(array("name" => $option_name));
    return $option_id->fetch()['option_id'];
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
    $details_stats = $db->prepare('SELECT DATE(p.inscription_date) day, COUNT(p.participant_id) nombre FROM events e LEFT JOIN participants p ON p.event_id = e.event_id WHERE e.event_id=:event_id and status != "A" GROUP BY DATE(p.inscription_date) ORDER BY day DESC LIMIT 0,7');
    $details_stats->execute(array('event_id' => $event_id));
    return $details_stats->fetchAll();
}
function get_event_payments_stats($event_id)
{
    global $db;
    $payment_stats = $db->prepare('SELECT payement, COUNT(*) nombre FROM participants p WHERE event_id=:event_id and status != "A" GROUP BY payement ORDER BY nombre');
    $payment_stats->execute(array('event_id' => $event_id));
    return $payment_stats->fetchAll();
}