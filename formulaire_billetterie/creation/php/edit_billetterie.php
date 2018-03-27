<?php

require __DIR__ . '/../../general_requires/_header.php';

if(!empty($_POST))
{
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);
    $event_id=$_GET['event_id'];

    check_and_prepare_data();

    $scoobydoo_infos = get_scoobydoo_event_infos(array("event_id" => $event_id));
    $fundation_id = $scoobydoo_infos['fundation_id'];

    $scoobydoo_ids = json_decode($scoobydoo_infos['scoobydoo_category_ids']);
    $scoobydoo_event_category_id = $scoobydoo_ids->scoobydoo_event_id;
    $scoobydoo_promos_id = $scoobydoo_ids->scoobydoo_promos_id;
    $scoobydoo_options_id = $scoobydoo_ids->scoobydoo_options_id;

    $previous_event_accessibilty = get_specification_details($event_id);

    //Table events

    $payutcClient->setCategory(array("obj_id" => $scoobydoo_event_category_id, "name" => $event->name, "parent_id" => null, "fun_id" => $fundation_id))->success;

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

    foreach($event_promos as $promo_data)
    {
        $found=0;
        foreach($previous_event_accessibilty as $key => $previous_promo)
        {
            if($promo_data->promo_id == $previous_promo['promo_id'] && $promo_data->site_id == $previous_promo['site_id'])
            {

                $payutcClient->setProduct(array(
                    "obj_id" => $previous_promo['scoobydoo_article_id'],
                    "name" => $event->name . " Prix " . $promo_data->promo . " " . $promo_data->site,
                    "parent" => $scoobydoo_promos_id,
                    "prix" => 100*$promo_data->price,
                    "stock" => $promo_data->quota,
                    "image" => '',
                    "alcool" => 0,
                    "cotisant" => false,
                    "fun_id" => $fundation_id
                    ));

                $table_specifications = array(
                    "event_id" => $event_id,
                    "site_id" => $promo_data->site_id,
                    "promo_id" => $promo_data->promo_id,
                    "price" => $promo_data->price,
                    "quota" => $promo_data->quota,
                    "guest_number" => $promo_data->guest_number
                    );
                update_specification_details($table_specifications);
                $found=1;
                unset($previous_event_accessibilty[$key]);
                break;
            }
        }
        if($found==0)
        {
            $scoobydoo_article_id = $payutcClient->setProduct(array(
                "name" => $event->name . " Prix " . $promo_data->promo . " " . $promo_data->site,
                "parent" => $scoobydoo_promos_id,
                "prix" => 100*$promo_data->price,
                "stock" => $promo_data->quota,
                "image" => '',
                "alcool" => 0,
                "cotisant" => false,
                "fun_id" => $fundation_id
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
    }

    delete_previous_option_accessibility($event_id);

    foreach($previous_event_accessibilty as $promo)
    {
        $payutcClient->deleteProduct(array("obj_id" => $promo['scoobydoo_article_id'], "fun_id" => $fundation_id));
        delete_specification_details(array("event_id" => $event_id, "promo_id" => $promo['promo_id'], "site_id" => $promo['site_id']));
    }

    //options & its accessibilities


    $previous_options = get_options($event_id);

    foreach($options as &$option)
    {
        if(isset($option->option_id))//Option déjà définie
        {
            foreach($previous_options as $key => $previous_option)
            {
                $previous_option_id = $previous_option['option_id'];
                if($option->option_id == $previous_option_id)
                {
                    $previous_specifications = json_decode($previous_option['specifications']);
                    if($option->type == "Checkbox")
                    {
                        $article_id = $previous_specifications->scoobydoo_article_id;
                        $payutcClient->setProduct(array(
                            "obj_id" => $article_id,
                            "name" => $event->name . " Option " . $option->name,
                            "parent" => $scoobydoo_options_id,
                            "prix" => 100*$option->type_specification->price,
                            "stock" => $option->quota,
                            "image" => '',
                            "alcool" => 0,
                            "cotisant" => false,
                            "fun_id" => $fundation_id
                            ));
                        $option->type_specification->scoobydoo_article_id=$article_id;
                    }
                    elseif($option->type == "Select")
                    {
                        foreach($option->type_specification as &$select_option)
                        {
                            $found_select_option = false;
                            foreach($previous_specifications as $key_s_option => $previous_select_option)
                            {
                                if($previous_select_option->name == $select_option->name)
                                {
                                    $found_select_option = true;
                                    $article_id = $previous_select_option->scoobydoo_article_id;

                                    $payutcClient->setProduct(array(
                                        "obj_id" => $article_id,
                                        "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                                        "parent" => $scoobydoo_options_id,
                                        "prix" => 100*$select_option->price,
                                        "stock" => $select_option->quota,
                                        "image" => '',
                                        "alcool" => 0,
                                        "cotisant" => false,
                                        "fun_id" => $fundation_id
                                        ));

                                    $select_option->scoobydoo_article_id = $article_id;
                                    unset($previous_specifications[$key_s_option]);
                                }
                            }
                            if(!$found_select_option)
                            {
                                $article_id = $payutcClient->setProduct(array(
                                    "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                                    "parent" => $scoobydoo_options_id,
                                    "prix" => 100*$select_option->price,
                                    "stock" => $select_option->quota,
                                    "image" => '',
                                    "alcool" => 0,
                                    "cotisant" => false,
                                    "fun_id" => $fundation_id
                                    ))->success;
                                $select_option->scoobydoo_article_id = $article_id;
                            }
                        }
                        foreach($previous_specifications as $previous_specification)
                        {
                            $payutcClient->deleteProduct(array("obj_id" => $previous_specification->scoobydoo_article_id, "fun_id" => $fundation_id));
                        }
                    }
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
                    update_option($table_option_data);
                    unset($previous_options[$key]);
                    break;
                }
            }
        }
        else//Nouvelle option
        {
            if($option->type == "Checkbox")
            {
                $article_id = $payutcClient->setProduct(array(
                    "name" => $event->name . " Option " . $option->name,
                    "parent" => $scoobydoo_options_id,
                    "prix" => 100*$option->type_specification->price,
                    "stock" => $option->quota,
                    "image" => '',
                    "alcool" => 0,
                    "cotisant" => false,
                    "fun_id" => $fundation_id
                    ))->success;
                $option->type_specification->scoobydoo_article_id = $article_id;
            }
            elseif($option->type == "Select")
            {
                foreach($option->type_specification as &$select_option)
                {
                    $article_id = $payutcClient->setProduct(array(
                        "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                        "parent" => $scoobydoo_options_id,
                        "prix" => 100*$select_option->price,
                        "stock" => $select_option->quota,
                        "image" => '',
                        "alcool" => 0,
                        "cotisant" => false,
                        "fun_id" => $fundation_id
                        ))->success;
                    $select_option->scoobydoo_article_id = $article_id;
                }
            }
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
            $option_id = insert_option($table_option_data);
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
    foreach($previous_options as $previous_option)
    {
        $specifications= json_decode($previous_option['specifications']);
        if($previous_option['type']=='Select')
        {
            foreach($specifications as $specification)
            {
                $payutcClient->deleteProduct(array("obj_id" => $specification->scoobydoo_article_id, "fun_id" => $fundation_id));
            }
        }
        elseif($previous_option['type']=='Checkbox')
        {
            $payutcClient->deleteProduct(array("obj_id" => $specifications->scoobydoo_article_id, "fun_id" => $fundation_id));
        }
        delete_option(array("event_id" => $event_id, "option_id" => $previous_option['option_id']));
    }
    echo 'Les modifications ont bien été pris en compte !';
}
else
{
    set_alert_style();
    add_error('Il y a eu un problème, la variable POST n\'est même pas définie...');
}