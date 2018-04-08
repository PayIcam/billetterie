<?php

require __DIR__ . '/../../general_requires/_header.php';

if(!empty($_POST))
{
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    check_and_prepare_data();

    //Table events

    $scoobydoo_event_id = $payutcClient->setCategory(array("name" => $event->name, "parent_id" => null, "fun_id" => $event->fundation_id))->success;
    $scoobydoo_promos_id = $payutcClient->setCategory(array("name" => 'Prix par promo', "parent_id" => $scoobydoo_event_id, "fun_id" => $event->fundation_id))->success;
    $scoobydoo_options_id = $payutcClient->setCategory(array("name" => 'Options', "parent_id" => $scoobydoo_event_id, "fun_id" => $event->fundation_id))->success;

    $scoobydoo_category_ids = json_encode(array("scoobydoo_event_id" => $scoobydoo_event_id, "scoobydoo_promos_id" => $scoobydoo_promos_id, "scoobydoo_options_id" => $scoobydoo_options_id));

    $table_event_data = array(
        "name" => $event->name,
        "description" => $event->description,
        "total_quota" => $event->quota,
        "ticketing_start_date" => $event->ticketing_start_date,
        "ticketing_end_date" => $event->ticketing_end_date,
        "is_active" => $event->is_active,
        "fundation_id" => $event->fundation_id,
        "scoobydoo_category_ids" => $scoobydoo_category_ids
        );
    $event_id = insert_event_details($table_event_data);

    //table promos_sites_specifications


    foreach($event_promos as $promo_data)
    {
        $scoobydoo_article_id = $payutcClient->setProduct(array(
            "name" => $event->name . " Prix " . $promo_data->promo . " " . $promo_data->site,
            "parent" => $scoobydoo_promos_id,
            "prix" => 100*$promo_data->price,
            "stock" => $promo_data->quota,
            "image" => '',
            "alcool" => 0,
            "cotisant" => false,
            "fun_id" => $event->fundation_id
            ))->success;

        $table_specifications = array(
            "event_id" => $event_id,
            "site_id" => $promo_data->site_id,
            "promo_id" => $promo_data->promo_id,
            "price" => $promo_data->price,
            "quota" => $promo_data->quota,
            "guest_number" => $promo_data->guest_number,
            "scoobydoo_article_id" => $scoobydoo_article_id
            );
        insert_specification_details($table_specifications);
    }

    //options & its accessibilities

    foreach($options as $option)
    {
        if($option->type == "Checkbox")
        {
            $scoobydoo_article_id = $payutcClient->setProduct(array(
                "name" => $event->name . " Option " . $option->name,
                "parent" => $scoobydoo_options_id,
                "prix" => 100*$option->type_specification->price,
                "stock" => $option->quota,
                "image" => '',
                "alcool" => 0,
                "cotisant" => false,
                "fun_id" => $event->fundation_id
                ))->success;

            $option->type_specification->scoobydoo_article_id = $scoobydoo_article_id;
        }
        elseif($option->type == "Select")
        {
            foreach($option->type_specification as &$select_option)
            {
                $scoobydoo_article_id = $payutcClient->setProduct(array(
                    "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                    "parent" => $scoobydoo_options_id,
                    "prix" => 100*$select_option->price,
                    "stock" => $select_option->quota,
                    "image" => '',
                    "alcool" => 0,
                    "cotisant" => false,
                    "fun_id" => $event->fundation_id
                    ))->success;

                $select_option->scoobydoo_article_id = $scoobydoo_article_id;
            }
        }

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
        $option_id = insert_option($table_option_data);

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
    set_alert_style("Erreur routing");
    add_error("Vous n'êtes pas censé ouvrir cette page directement.");
}

