<?php

require '../../config.php';
require '../../general_requires/db_functions.php';
require '../../general_requires/display_functions.php';
require 'requires/db_functions.php';
require 'requires/controller_functions.php';

$db = connect_to_db($_CONFIG['ticketing']);

if(isset($_POST))
{
    //Table events
    $table_event_data = array(
        "name" => $_POST['event_name'],
        "description" => $_POST['event_description'],
        "total_quota" => $_POST['event_quota'],
        "ticketing_start_date" => date('Y-m-d H:i:s', date_create_from_format('d/m/Y h:i a', $_POST['ticketing_start_date'])->getTimestamp()),//Jquery sends a weird format for dates, I have to specificate this format, then have to convert to Sql's one
        "ticketing_end_date" => date('Y-m-d H:i:s', date_create_from_format('d/m/Y h:i a', $_POST['ticketing_end_date'])->getTimestamp()),
        "is_active" => $_POST['event_is_active'],
        "has_guests" => $_POST['guests']
        );
    insert_event_details($table_event_data);
    $event_id = $db->lastInsertId();

    //table promos_sites_specifications
    $event_accessibility_json = $_POST['event_accessibility_json'];
    $event_promos = json_decode($event_accessibility_json);
    foreach($event_promos as $promo_data)
    {
        $table_specifications = array(
            "event_id" => $event_id,
            "site_id" => get_site_id($promo_data->site),
            "promo_id" => get_promo_id($promo_data->promo),
            "price" => $promo_data->price,
            "quota" => $promo_data->quota,
            "guest_number" => $promo_data->guest_number
            );
        insert_specification_details($table_specifications);
    }

    //just for checking
    $has_options = $_POST['options'];
    $has_permanents = $_POST['permanents'];
    $has_graduated = $_POST['graduated_icam'];

    //options &
    if(isset($_POST['option_details_json']))
    {
        $options_json = $_POST['option_details_json'];
        $options = json_decode($options_json);
        foreach($options as $option)
        {
            $quota = ($option->quota=='') ? null : $option->quota;

            $table_option_data = array(
                "name" => $option->name,
                "description" => $option->description,
                "is_active" => $option->is_active,
                "is_mandatory" => $option->is_mandatory,
                "type" => $option->type,
                "quota" => $quota,
                "specifications" => json_encode($option->type_specification),
                "event_id" => $event_id
                );
            insert_option($table_option_data);
            $option_id = $db->lastInsertId();

            foreach($option->accessibility as $promo_data)
            {
                if($promo_data->site=='Tous')
                {
                    $sites_id = get_sites_id();
                    foreach($sites_id as $site_id)
                    {
                        $table_specifications = array(
                            "event_id" => $event_id,
                            "site_id" => $site_id['site_id'],
                            "promo_id" => get_promo_id($promo_data->promo),
                            "option_id" => $option_id
                            );
                        insert_option_accessibility($table_specifications);
                    }
                }
                else
                {
                    $option_accessibility = array(
                        "event_id" => $event_id,
                        "site_id" => get_site_id($promo_data->site),
                        "promo_id" => get_promo_id($promo_data->promo),
                        "option_id" => $option_id
                        );
                    insert_option_accessibility($option_accessibility);
                }
            }
        }
    }
}
else
{
    echo "Vous n'êtes pas censé ouvrir cette page directement.";
}

