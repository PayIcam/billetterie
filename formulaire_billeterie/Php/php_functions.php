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

function insert_as_select_option($array_to_insert)
{
    foreach ($array_to_insert as $element)
    {
        echo '<option>'. $element[0] .'</option>';
    }
}