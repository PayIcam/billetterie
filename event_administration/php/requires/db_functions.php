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
    $id = $db->query('SELECT site_id FROM sites');
    return $id->fetchAll();
}

function insert_event_details($table_event_data)
{
    global $db;
    $event_insertion = $db->prepare('INSERT INTO events(name, description, conditions, is_active, ticketing_start_date, ticketing_end_date, total_quota, fundation_id, scoobydoo_category_ids, created_by) VALUES (:name, :description, :conditions, :is_active, :ticketing_start_date, :ticketing_end_date, :total_quota, :fundation_id, :scoobydoo_category_ids, :created_by)');
    $event_insertion->execute($table_event_data);
    return $db->lastInsertId();
}
function update_event_image($data) {
    global $db;
    $event_update = $db->prepare('UPDATE events SET img_path = :img_path WHERE event_id = :event_id');
    return $event_update->execute($data);
}
function update_event_details($table_event_data)
{
    global $db;
    $event_update = $db->prepare('UPDATE events SET name = :name, description = :description, conditions = :conditions, is_active = :is_active, ticketing_start_date = :ticketing_start_date, ticketing_end_date = :ticketing_end_date, total_quota = :total_quota WHERE event_id = :event_id');
    return $event_update->execute($table_event_data);
}
function get_scoobydoo_event_infos($event_id)
{
    global $db;
    $scoobydoo_ids = $db->prepare('SELECT scoobydoo_category_ids, fundation_id FROM events WHERE event_id = :event_id');
    $scoobydoo_ids->execute(array('event_id' => $event_id));
    return $scoobydoo_ids->fetch();
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
    $option_insertion = $db->prepare('INSERT INTO options(name, description, is_active, is_mandatory, type, quota, event_id) VALUES (:name, :description, :is_active, :is_mandatory, :type, :quota, :event_id)');
    $option_insertion->execute($table_option_data);
    return $db->lastInsertId();
}
function update_option($table_option_data)
{
    global $db;
    $option_update = $db->prepare('UPDATE options SET name= :name, description= :description, is_active= :is_active, is_mandatory= :is_mandatory, type= :type, quota= :quota, event_id= :event_id WHERE event_id= :event_id and option_id= :option_id and is_removed=0');
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
/**
 * Cette fonction permet de savoir si des participants d'une certaine promo (promo+site) ont pris ou non leur place.
 * Le principe est de compter le nombre de lignes ou des participants appartiennent à la promo. Si c'est 0 on return true, false sinon
 * @param  array $ids [array('event_id' => , 'site_id' => , 'promo_id' => )]
 * @return boolean
 */
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

/**
 * Cette fonction permet de savoir si des participants ont pris une certaine option.
 * Le principe est de compter le nombre de lignes ou les participants ont pris l'option. Si c'est 0 on return true, false sinon
 * @param  array $ids [array('event_id' => , 'option_id' => )]
 * @return boolean
 */
function can_delete_option($ids)
{
    global $db;
    $count_option = $db->prepare('SELECT COUNT(*) rows FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id=pho.choice_id WHERE pho.event_id= :event_id and oc.option_id= :option_id');
    $count_option->execute($ids);
    return $count_option->fetch()['rows'] == 0 ? true : false;
}
/**
 * Cette fonction permet de savoir si des participants ont pris une certaine sous-option.
 * Le principe est de compter le nombre de lignes ou les participants ont pris la sous-option. Si c'est 0 on return true, false sinon
 * @param  array $ids [array('event_id' => , 'choice_id' => )]
 * @return boolean
 */
function can_delete_option_choice($ids)
{
    global $db;
    $count_option = $db->prepare('SELECT COUNT(*) rows FROM participant_has_options pho LEFT JOIN option_choices oc ON oc.choice_id=pho.choice_id WHERE pho.event_id= :event_id and pho.choice_id= :choice_id');
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

/**
 * Il y a un problème avec les options obligatoires. En effet, si l'option est obligatoire, il faut absolument que le participant la prenne. S'il ne l'avait pas, et qu'il allait sur la page d'edit, il aurait des soucis au moment d'envoyer les infos (c'est normal).
 * Il faut donc faire très attention avec ces options. On peux passer une option d'obligatoire, à facultative quand on veux, mais pas l'inverse.
 * Si un seul participant a pris sa place, est potentiellement visé par l'option, mais ne l'avait pas prise, alors, impossible de passer cette option en obligatoire.
 * Cela irait si le participant avait pris l'option ceci dit
 *
 * Une fois ceci compris, le nom de la fonction pose clairement ce qui va être retourné
 * On va compter le nombre de lignes ou on aurait des participants avec des problèmes si l'option devenait obligatoire, si c'est 0, on renvoie false, true sinon.
 * @param  array $ids [array('option_id' => , 'event_id' => )]
 * @return boolean      [description]
 */
function a_participant_would_have_to_pay_obliged_option($ids)
{
    global $db;
    $count_option = $db->prepare('
    SELECT COUNT(*) nb_participants_without_option FROM participants p
    LEFT JOIN promo_site_has_options psho ON psho.promo_id=p.promo_id and psho.site_id=p.site_id and psho.site_id=p.site_id and psho.event_id=p.event_id
    WHERE p.status!="A" and psho.option_id=:option_id and participant_id
    NOT IN(SELECT p.participant_id FROM participants p
        LEFT JOIN participant_has_options pho ON pho.participant_id=p.participant_id
        LEFT JOIN option_choices oc ON oc.choice_id = pho.choice_id
        WHERE p.event_id=:event_id and oc.option_id=:option_id and p.status !="A" and pho.status!="A")
    ');
    $count_option->execute($ids);
    return $count_option->fetch()['nb_participants_without_option'] == 0 ? false : true;
}

/**
 * Retoune true si des participants ont djéà pris leur place. C'est le même problème qu'au dessus, mais en prenant compte d'une toute nouvelle option. C'est la carte de la simplicité qui est jouée ici.
 * @param  [mixed] $event_id
 * @return [boolean]           [description]
 */
function participants_already_took_places($event_id)
{
    global $db;
    $count_promo = $db->prepare('SELECT COUNT(*) current_total_quota FROM participants WHERE event_id= :event_id and status IN("V", "W")');
    $count_promo->execute(array("event_id" => $event_id));
    return $count_promo->fetch()['current_total_quota'] == 0 ? false : true;
}

/**
 * Le code est assez clair, il y a cette distinction, parce qu'il n'y a qu'un choix dans un checkbox, et plusieurs dans un select
 */
function insert_option_choices($data, $type)
{
    global $db;
    if($type=='Checkbox')
    {
        $insertion = $db->prepare('INSERT INTO option_choices(price, scoobydoo_article_id, option_id) VALUES (:price, :scoobydoo_article_id, :option_id)');
        return $insertion->execute($data);
    }
    elseif($type=="Select")
    {
        foreach($data as $choice_data)
        {
            $insertion = $db->prepare('INSERT INTO option_choices(price, scoobydoo_article_id, name, quota, is_removed, option_id) VALUES (:price, :scoobydoo_article_id, :name ,:quota ,:is_removed, :option_id)');
            $insertion->execute($choice_data);
        }
    }
}
function update_option_choices($data, $type)
{
    global $db;
    if($type=='Checkbox')
    {
        $insertion = $db->prepare('UPDATE option_choices SET price=:price WHERE choice_id=:choice_id ');
        return $insertion->execute($data);
    }
    elseif($type=="Select")
    {
        foreach($data as $choice_data)
        {
            $insertion = $db->prepare('UPDATE option_choices SET price=:price, name=:name, quota=:quota WHERE choice_id=:choice_id ');
            $insertion->execute($choice_data);
        }
    }
}

function remove_option_choice($choice_id)
{
    global $db;
    $removal = $db->prepare('UPDATE option_choices SET is_removed=1 WHERE choice_id=:choice_id');
    $removal->execute(array('choice_id' => $choice_id));
}
function delete_option_choice($choice_id)
{
    global $db;
    $removal = $db->prepare('DELETE FROM option_choices WHERE choice_id=:choice_id');
    $removal->execute(array('choice_id' => $choice_id));
}

function get_choice_article_id($choice_id)
{
    global $db;
    $option_choices = $db->prepare('SELECT scoobydoo_article_id FROM option_choices WHERE choice_id=:choice_id');
    $option_choices->execute(array('choice_id' => $choice_id));
    return $option_choices->fetch()['scoobydoo_article_id'];
}

function get_previous_choices($option_id, $updated_choice_ids)
{
    global $db;
    $option_choices = $db->prepare("SELECT * FROM option_choices WHERE option_id=:option_id and choice_id NOT IN ('$updated_choice_ids')");
    $option_choices->execute(array('option_id' => $option_id));
    return $option_choices->fetchAll();
}

function get_current_options($event_id)
{
    global $db;
    $option_query = $db->prepare('SELECT * FROM options WHERE event_id=:event_id and is_removed=0');
    $option_query->execute(array('event_id'=>$event_id));
    return $option_query->fetchAll();
}

function event_has_option($ids)
{
    global $db;
    $option = $db->prepare('SELECT * FROM options WHERE event_id = :event_id and option_id = :option_id and is_removed=0');
    $option->execute($ids);
    $option = $option->fetch();
    return empty($option) ? false : true;
}
