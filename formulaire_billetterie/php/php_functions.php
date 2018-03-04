<?php

function insert_as_select_option($array_to_insert)
{
    foreach ($array_to_insert as $element)
    {
        echo '<option>'. $element[0] .'</option>';
    }
}
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
function insert_event_accessibility_rows($promos_specifications)
{
    $numero = 1;
    foreach($promos_specifications as $promo_specifications)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= get_site_name($promo_specifications['site_id']) ?></td>
            <td><?= get_promo_name($promo_specifications['promo_id']) ?></td>
            <td><?= $promo_specifications['price']?></td>
            <td><?= $promo_specifications['quota']?></td>
            <td><?= $promo_specifications['guest_number']?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}
function insert_option_accessibility_rows($promo_options)
{
    $numero = 1;
    foreach($promo_options as $promo_option)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= get_site_name($promo_option['site_id']) ?></td>
            <td><?= get_promo_name($promo_option['promo_id']) ?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}
function insert_option_select_rows($option_specifications)
{
    $numero = 1;
    foreach($option_specifications as $option_specification)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= $option_specification->name ?></td>
            <td><?= $option_specification->price ?></td>
            <td><?= $option_specification->quota ?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}
function insert_select_options($option_specifications)
{
    foreach($option_specifications as $option_specification)
    {
        ?>
        <option><?= $option_specification->name . '(' . $option_specification->price . ')' ?></option>
        <?php
    }
}
function add_options_previously_defined($options)
{
    global $db;
    global $event_id;
    global $promos_specifications;

    $compteur=0;
    foreach($options as $option)
    {
        $compteur++;

        $promo_options_query = $db->prepare('SELECT * FROM promo_site_has_options WHERE event_id=:event_id and option_id=:option_id');
        $promo_options_query->execute(array('event_id'=>$event_id, 'option_id'=>$option['option_id']));
        $promo_options = $promo_options_query->fetchAll();

        if(count($promo_options) == count($promos_specifications))
        {
            $all_opt = array("everyone_has_option" => 1);
        }
        else
        {
            $all_opt = array("everyone_has_option" => 0);
        }
        $option = array_merge($option, $all_opt);

        $option_specifications = json_decode($option['specifications']);
        add_option_html_code($compteur, $option, $option_specifications, $promo_options);
    }
}
function get_event_radio_values($promos_specifications)
{
    $guests = 0;
    $permanents = 0;
    $graduated = 0;

    global $list_graduated_promos;

    foreach($promos_specifications as $promo_specifications)
    {
        if(get_promo_name($promo_specifications['promo_id']) == 'Invités')
        {
            $guests = 1;
        }
        elseif(get_promo_name($promo_specifications['promo_id']) == 'Permanents')
        {
            $permanents = 1;
        }
        elseif(in_array(get_promo_name($promo_specifications['promo_id']), $list_graduated_promos))
        {
            $graduated = 1;
        }
    }
    return array("guests" => $guests, "permanents" => $permanents, "graduated" => $graduated);
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
function get_options_from_event($event_id)
{
    global $db;
    $option_ids = $db->prepare('SELECT option_id FROM options where event_id = :event_id');
    $option_ids->execute(array("event_id"=>$event_id));
    return $option_ids->fetchAll();
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