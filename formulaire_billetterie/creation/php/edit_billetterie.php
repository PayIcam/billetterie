<?php

require '../../general_requires/display_functions.php';

if(!empty($_POST))
{
    require '../../config.php';
    require '../../general_requires/db_functions.php';
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);
    $event_id=$_GET['event_id'];

    check_and_prepare_data();

    //Table events

    $table_event_data = array(
        "event_id" => $event_id,
        "name" => $event->name,
        "description" => $event->description,
        "total_quota" => $event->quota,
        "ticketing_start_date" => $event->ticketing_start_date,
        "ticketing_end_date" => $event->ticketing_end_date,
        "is_active" => $event->is_active
        );
    update_event_details($table_event_data);

    //table promos_sites_specifications

    delete_previous_option_accessibility(array("event_id" => $event_id));
    delete_specification_details($event_id);
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

    $options_precedentes_editees = array();
    $previous_options_ids = get_option_ids_from_event($event_id);
    foreach($options as $option)
    {
        if(isset($option->option_id))//Option déjà définie
        {
            foreach($previous_options_ids as $previous_options_id)
            {
                $previous_option_id = $previous_options_id['option_id'];
                if($option->option_id == $previous_option_id)
                {
                    $option_id = $option->option_id;
                    $table_option_data = array(
                        "event_id" => $event_id,
                        "option_id" => $option_id,
                        "name" => $option->name,
                        "description" => $option->description,
                        "is_active" => $option->is_active,
                        "is_mandatory" => $option->is_mandatory,
                        "type" => $option->type,
                        "quota" => $option->quota,
                        "specifications" => json_encode($option->type_specification),
                        );
                    array_push($options_precedentes_editees, $option_id);
                    update_option($table_option_data);
                    break;
                }
            }
        }
        else//Nouvelle option
        {
            $table_option_data = array(
                "event_id" => $event_id,
                "name" => $option->name,
                "description" => $option->description,
                "is_active" => $option->is_active,
                "is_mandatory" => $option->is_mandatory,
                "type" => $option->type,
                "quota" => $option->quota,
                "specifications" => json_encode($option->type_specification),
                );
            insert_option($table_option_data);
            $option_id = $db->lastInsertId();
        }

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
    if(count($options_precedentes_editees) < count($previous_options_ids))
    {
        foreach($previous_options_ids as $previous_options_id)
        {
            $keep = 0;
            $previous_option_id=$previous_options_id['option_id'];
            foreach($options_precedentes_editees as $option_id_editee)
            {
                if($option_id_editee==$previous_option_id)
                {
                    $keep=1;
                    break;
                }
            }
            if($keep==0)
            {
                delete_option(array("event_id" => $event_id, "option_id" => $previous_option_id));
            }
        }
    }
    echo 'Les modifications ont bien été pris en compte !';
}

else
{
    set_alert_style();
    add_error('Il y a eu un problème, la variable POST n\'est même pas définie...');
}