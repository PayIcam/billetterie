<?php

require '../../general_requires/display_functions.php';

if(!empty($_POST))
{
    require '../../config.php';
    require '../../general_requires/db_functions.php';
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    check_and_prepare_data();

    //Table events

    $table_event_data = array(
        "name" => $event->name,
        "description" => $event->description,
        "total_quota" => $event->quota,
        "ticketing_start_date" => $event->ticketing_start_date,
        "ticketing_end_date" => $event->ticketing_end_date,
        "is_active" => $event->is_active
        );
    insert_event_details($table_event_data);
    $event_id = $db->lastInsertId();

    //table promos_sites_specifications

    foreach($event_promos as $promo_data)
    {
        $table_specifications = array(
            "event_id" => $event_id,
            "site_id" => $promo_data->site_id,
            "promo_id" => $promo_data->promo_id,
            "price" => $promo_data->price,
            "quota" => $promo_data->quota,
            "guest_number" => $promo_data->guest_number
            );
        insert_specification_details($table_specifications);
    }

    //options & its accessibilities

    foreach($options as $option)
    {
        $table_option_data = array(
            "name" => $option->name,
            "description" => $option->description,
            "is_active" => $option->is_active,
            "is_mandatory" => $option->is_mandatory,
            "type" => $option->type,
            "quota" => $option->quota,
            "specifications" => json_encode($option->type_specification),
            "event_id" => $event_id
            );
        insert_option($table_option_data);
        $option_id = $db->lastInsertId();

        foreach($option->accessibility as $promo_data)
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
    echo 'Les informations ont bien été pris en compte !';
}
else
{
    set_alert_style();
    add_error("Vous n'êtes pas censé ouvrir cette page directement.");
}

