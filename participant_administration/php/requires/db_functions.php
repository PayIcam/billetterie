<?php

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
function get_select_mandatory_options($ids)
{
    global $db;
    $select_mandatory_options = $db->prepare('SELECT * FROM options o LEFT JOIN promo_site_has_options psho ON o.option_id = psho.option_id LEFT JOIN option_choices oc ON oc.option_id=o.option_id WHERE is_active=1 and o.is_removed=0 and oc.is_removed=0 and type="Select" and is_mandatory=1 and o.event_id=:event_id and promo_id=:promo_id and site_id=:site_id ORDER BY oc.option_id');
    $select_mandatory_options->execute($ids);
    return $select_mandatory_options->fetchAll();
}
function get_optional_options($ids)
{
    global $db;
    $optional_options = $db->prepare('SELECT DISTINCT o.option_id, o.name, description, type, o.event_id FROM options o LEFT JOIN option_choices oc ON oc.option_id=o.option_id LEFT JOIN promo_site_has_options psho ON o.option_id = psho.option_id WHERE (is_active=1 and o.is_removed=0 and oc.is_removed=0 and o.event_id=:event_id and promo_id=:promo_id and site_id=:site_id and o.option_id NOT IN(SELECT option_id FROM participant_has_options phoo LEFT JOIN option_choices occ ON occ.choice_id=phoo.choice_id WHERE participant_id=:participant_id)) and ((type="Select" and is_mandatory=0) or (type="Checkbox"))');
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
    $optional_options = $db->prepare('SELECT COUNT(*) matches FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id=pho.choice_id WHERE participant_id=:participant_id and oc.option_id=:option_id and pho.event_id=:event_id');
    $optional_options->execute($ids);
    return $optional_options->fetch()['matches'] == 1 ? true : false;
}

function get_event_details_stats($event_id)
{
    global $db;
    $details_stats = $db->prepare('
        SELECT e.*, COUNT(p.participant_id) total_count, SUM(IF(p.bracelet_identification IS NOT NULL, 1, 0)) total_bracelet_count, SUM(IF(pr.still_student = 1, 1, 0)) student_count, SUM(IF(pr.still_student = 0, 1, 0)) graduated_count, SUM(IF(pr.promo_name="Invités", 1, 0)) guests_count, COUNT(a.participant_id) arrival_count
        FROM events e
        LEFT JOIN participants p on p.event_id=e.event_id
        LEFT JOIN promos pr ON pr.promo_id=p.promo_id
        LEFT JOIN promos_site_specifications pss ON p.promo_id = pss.promo_id and p.site_id = pss.site_id and p.event_id=pss.event_id
        LEFT JOIN arrivals a ON a.participant_id=p.participant_id and a.event_id=p.event_id
        WHERE e.event_id=:event_id and p.status!="A"');
    $details_stats->execute(array('event_id' => $event_id));

    $type_quotas = $db->prepare('SELECT SUM(IF(pr.still_student=0, quota, 0)) graduated_quota, SUM(IF(pr.still_student=1, quota, 0)) student_quota, SUM(IF(pr.promo_name="Invités", quota, 0)) guest_quota FROM promos_site_specifications pss LEFT JOIN promos pr ON pr.promo_id=pss.promo_id WHERE pss.event_id=:event_id and (pr.still_student!=2 or pr.promo_name="Invités")');
    $type_quotas->execute(array('event_id' => $event_id));

    $options_count = $db->prepare('SELECT COUNT(DISTINCT participant_id) options_count FROM participant_has_options WHERE event_id=:event_id');
    $options_count->execute(array('event_id' => $event_id));

    return array_merge($details_stats->fetch(), $type_quotas->fetch(), $options_count->fetch());
}
function get_promo_specification_details_stats($event_id)
{
    global $db;
    $details_stats = $db->prepare('
        SELECT pr.promo_name, s.site_name, pss.quota, pss.guest_number, SUM(IF(p.status IN ("V", "W"), 1, 0)) promo_count, SUM(IF(p.bracelet_identification IS NULL or p.status="A", 0, 1)) bracelet_count
        FROM promos_site_specifications pss
        LEFT JOIN participants p ON p.promo_id = pss.promo_id and p.site_id = pss.site_id and pss.event_id=p.event_id
        LEFT JOIN promos pr ON pr.promo_id=pss.promo_id
        LEFT JOIN sites s ON s.site_id=pss.site_id
        WHERE p.event_id=:event_id and status != "A"
        GROUP BY pss.promo_id, pss.site_id, pss.quota, pss.guest_number');
    $details_stats->execute(array('event_id' => $event_id));
    $details_stats = $details_stats->fetchAll();
    $guest_promo_count = $db->prepare('SELECT COUNT(ihg.icam_id) invited_guests
        FROM promos_site_specifications pss
        LEFT JOIN participants p ON p.promo_id = pss.promo_id and p.site_id = pss.site_id and pss.event_id=p.event_id
        LEFT JOIN icam_has_guests ihg on ihg.icam_id=p.participant_id
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

function participant_has_arrived($participant_id)
{
    global $db;
    $is_in = $db->prepare('SELECT * FROM arrivals WHERE participant_id=:participant_id');
    $is_in->execute(array('participant_id' => $participant_id));
    return !empty($is_in->fetchAll());
}

function get_participant_event_arrival_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT p.*, COUNT(a.participant_id) arrival FROM participants p LEFT JOIN arrivals a ON a.participant_id=p.participant_id WHERE p.event_id = :event_id and p.participant_id = :participant_id and status="V" ');
    $participant_data->execute($ids);
    return $participant_data->fetch();
}

function participant_arrives($ids)
{
    global $db;
    $arrival = $db->prepare('INSERT INTO arrivals(participant_id, event_id) VALUES (:participant_id, :event_id)');
    return $arrival->execute($ids);
}

function participant_leaves($ids)
{
    global $db;
    $arrival = $db->prepare('DELETE FROM arrivals WHERE participant_id=:participant_id and event_id=:event_id');
    return $arrival->execute($ids);
}

function get_arrival_number($event_id)
{
    global $db;
    $arrival_number = $db->prepare('SELECT COUNT(*) FROM arrivals WHERE event_id=:event_id');
    $arrival_number->execute(array('event_id' => $event_id));
    return $arrival_number->fetch()['COUNT(*)'];
}

function get_participant_event_data($ids)
{
    global $db;
    $participant_data = $db->prepare('SELECT * FROM participants WHERE event_id = :event_id and participant_id = :participant_id and status="V" ');
    $participant_data->execute($ids);
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

function get_participant_options_and_choices($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id = pho.choice_id WHERE event_id=:event_id and participant_id=:participant_id and status="V" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function get_pending_options_and_choices($ids)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id = pho.choice_id WHERE event_id=:event_id and participant_id=:participant_id and status="W" ');
    $option_query->execute($ids);
    return $option_query->fetchAll();
}

function participant_can_have_choice($ids)
{
    global $db;
    $rows_number = $db->prepare('SELECT COUNT(*) FROM option_choices oc
        LEFT JOIN options o ON o.option_id=oc.option_id
        LEFT JOIN promo_site_has_options psho ON psho.option_id=o.option_id and psho.event_id=o.event_id
        LEFT JOIN promos_site_specifications pss ON pss.promo_id=psho.promo_id and pss.site_id=psho.site_id and pss.event_id=psho.event_id
        LEFT JOIN participants p ON p.promo_id=pss.promo_id and p.site_id=pss.site_id and p.event_id=pss.event_id
        WHERE p.participant_id=:participant_id and oc.choice_id=:choice_id');
    $rows_number->execute($ids);
    return $rows_number->fetch()['COUNT(*)']==1 ? true : false;
}

function get_options_stats($event_id)
{
    global $db;
    $option_stats = $db->prepare('SELECT o.option_id, o.name, o.quota, o.type, COUNT(*) option_count FROM participant_has_options pho
        LEFT JOIN participants p ON p.participant_id=pho.participant_id
        LEFT JOIN option_choices oc ON pho.choice_id = oc.choice_id
        LEFT JOIN options o ON oc.option_id=o.option_id
        WHERE p.status!="A" and pho.status!="A" and p.event_id=:event_id
        GROUP BY o.option_id, o.name, o.quota, o.type');
    $option_stats->execute(array('event_id' => $event_id));
    $option_stats = $option_stats->fetchAll();
    foreach($option_stats as &$option)
    {
        $option['pourcentage_option'] = $option['quota'] !=0 ? round(100 * $option['option_count'] / $option['quota'], 2) . '%' : "undefined";
        if($option['type'] == 'Select')
        {
            $choices_stats = $db->prepare('SELECT oc.name, oc.quota, COUNT(*) choice_count FROM participant_has_options pho
                LEFT JOIN participants p ON p.participant_id=pho.participant_id
                LEFT JOIN option_choices oc ON pho.choice_id = oc.choice_id
                WHERE p.status!="A" and pho.status!="A" and p.event_id=:event_id and oc.option_id=:option_id
                GROUP BY oc.choice_id, oc.name, oc.quota
                ');
            $choices_stats->execute(array('option_id' => $option['option_id'], 'event_id' => $event_id));
            $choices_stats = $choices_stats->fetchAll();
            $option['choices'] = $choices_stats;
        }
        else
        {
             $option['choices'] = array();
        }
    }
    return $option_stats;
}