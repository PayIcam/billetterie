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

    //Table events
    $table_event_data = array(
        "event_id" => $event_id,
        "name" => $_POST['event_name'],
        "description" => $_POST['event_description'],
        "total_quota" => $_POST['event_quota'],
        "ticketing_start_date" => date('Y-m-d H:i:s', date_create_from_format('d/m/Y h:i a', $_POST['ticketing_start_date'])->getTimestamp()),
        "ticketing_end_date" => date('Y-m-d H:i:s', date_create_from_format('d/m/Y h:i a', $_POST['ticketing_end_date'])->getTimestamp()),
        "is_active" => isset($_POST['event_is_active']) ? 1:0,
        "has_guests" => $_POST['guests']
        );
    update_event_details($table_event_data);

    //table promos_sites_specifications
    $event_accessibility_json = $_POST['event_accessibility_json'];
    $event_promos = json_decode($event_accessibility_json);

    delete_previous_option_accessibility(array("event_id" => $event_id));
    delete_specification_details($event_id);
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
        $options_precedentes_editees = array();
        $previous_options_ids = get_option_ids_from_event($event_id);
        foreach($options as $option)
        {
            $quota = ($option->quota=='') ? null : $option->quota;
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
                            "quota" => $quota,
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
                    "quota" => $quota,
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
    }
}

else
{
    set_alert_style();
    add_error('Il y a eu un problème, la variable POST n\'est même pas définie...');
}