<?php

require __DIR__ . '/../../general_requires/_header.php';

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
    die();
}

if(!empty($_POST))
{
    require 'requires/db_functions.php';
    require 'requires/controller_functions.php';

    $event_id=$_GET['event_id'];

    check_and_prepare_data();

    $scoobydoo_infos = get_scoobydoo_event_infos($event_id);
    $fundation_id = $scoobydoo_infos['fundation_id'];

    $scoobydoo_ids = json_decode($scoobydoo_infos['scoobydoo_category_ids']);
    $scoobydoo_event_category_id = $scoobydoo_ids->scoobydoo_event_id;
    $scoobydoo_promos_id = $scoobydoo_ids->scoobydoo_promos_id;
    $scoobydoo_options_id = $scoobydoo_ids->scoobydoo_options_id;

    $previous_event_accessibilty = get_all_specification_details($event_id);

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
        if(can_delete_promo(array('event_id' => $event_id, 'promo_id' => $promo['promo_id'], 'site_id' => $promo['site_id'])))
        {
            delete_specification_details(array("event_id" => $event_id, "promo_id" => $promo['promo_id'], "site_id" => $promo['site_id']));
            $payutcClient->deleteProduct(array("obj_id" => $promo['scoobydoo_article_id'], "fun_id" => $fundation_id));
        }
        else
        {
            remove_promo(array('event_id' => $event_id, 'promo_id' => $promo['promo_id'], 'site_id' => $promo['site_id']));
        }
    }

    //options & its accessibilities

    $previous_options = get_current_options($event_id);

    foreach($options as &$option)
    {
        if(isset($option->option_id))//Option déjà définie
        {
            foreach($previous_options as $key => $previous_option)
            {
                $previous_option_id = $previous_option['option_id'];
                if($option->option_id == $previous_option_id)
                {
                    $previous_option_choices = get_option_choices($option->option_id);
                    if($option->type == "Checkbox")
                    {
                        $choice_id = $previous_option_choices[0]['choice_id'];
                        $article_id = get_choice_article_id($choice_id);

                        try
                        {
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
                        }
                        catch(Exception $e)
                        {
                            var_dump(array(
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
                            die();
                        }

                        $option->type_specification->scoobydoo_article_id=$article_id;

                        $previous_option_choices_data = array(
                            'choice_id' => $choice_id,
                            'price' => $option->type_specification->price,
                            );
                        update_option_choices($previous_option_choices_data, $option->type);
                    }
                    elseif($option->type == "Select")
                    {
                        foreach($option->type_specification as &$select_option)
                        {
                            $previous_option_choices_update_data = array();
                            $previous_option_choices_addition_data = array();
                            if(isset($select_option->choice_id))
                            {
                                $article_id = get_choice_article_id($select_option->choice_id);

                                try
                                {
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
                                }
                                catch(Exception $e)
                                {
                                    var_dump(array(
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
                                    die();
                                }

                                $previous_option_choices_update_data[] = array(
                                    'choice_id' => $select_option->choice_id,
                                    'price' => $select_option->price,
                                    'name' => $select_option->name,
                                    'quota' => $select_option->quota,
                                    );
                            }
                            else
                            {
                                $article_id = $payutcClient->setProduct(array(
                                    "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                                    "parent" => $scoobydoo_options_id,
                                    "prix" => 100*$select_option->price,
                                    "stock" => $select_option->quota,
                                    "image" => '',
                                    "alcool" => 0,
                                    "cotisant" => false,
                                    "fun_id" => $event->fundation_id
                                    ))->success;

                                $previous_option_choices_addition_data[] = array(
                                    'price' => $select_option->price,
                                    'scoobydoo_article_id' => $article_id,
                                    'name' => $select_option->name,
                                    'quota' => $select_option->quota,
                                    'is_removed' => 0,
                                    'option_id' => $option->option_id
                                    );
                            }
                        }
                        $updated_choice_ids = join("', '", array_column($option->type_specification, 'choice_id'));
                        $previous_choices = get_previous_choices($option->option_id, $updated_choice_ids);
                        foreach($previous_choices as $previous_choice)
                        {
                            die();
                            if(can_delete_option_choice(array('event_id' => $event_id, 'choice_id' => $choice_id)))
                            {
                                $payutcClient->deleteProduct(array("obj_id" => $previous_choice->scoobydoo_article_id, "fun_id" => $fundation_id));
                                delete_option_choice($choice_id);
                            }
                            else
                            {
                                remove_option_choice($choice_id);
                            }
                        }
                        update_option_choices($previous_option_choices_update_data, $option->type);
                        insert_option_choices($previous_option_choices_addition_data, $option->type);
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
                        );
                    update_option($table_option_data);
                    unset($previous_options[$key]);
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
                );
            $option_id = insert_option($table_option_data);

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

                $option_choices = array(
                    'price' => $option->type_specification->price,
                    'scoobydoo_article_id' => $article_id,
                    'option_id' => $option_id
                    );
            }
            elseif($option->type == "Select")
            {
                $option_choices = array();
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

                    $option_choices[] = array(
                        'price' => $select_option->price,
                        'scoobydoo_article_id' => $article_id,
                        'name' => $select_option->name,
                        'quota' => $select_option->quota,
                        'is_removed' => 0,
                        'option_id' => $option_id
                        );
                }
            }
            insert_option_choices($option_choices, $option->type);

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
        if(can_delete_option(array('event_id' => $previous_option['event_id'], 'option_id' => $previous_option['option_id'])))
        {
            $option_choices = get_option_choices($previous_option['option_id']);
            if($previous_option['type']=='Select')
            {
                foreach($option_choices as $option_choice)
                {
                    $payutcClient->deleteProduct(array("obj_id" => $option_choice['scoobydoo_article_id'], "fun_id" => $fundation_id));
                    delete_option_choice($option_choice['choice_id']);
                }
            }
            elseif($previous_option['type']=='Checkbox')
            {
                $payutcClient->deleteProduct(array("obj_id" => $option_choices[0]['scoobydoo_article_id'], "fun_id" => $fundation_id));
                delete_option_choice($option_choices[0]['choice_id']);
            }
            delete_option(array("event_id" => $event_id, "option_id" => $previous_option['option_id']));
        }
        else
        {
            remove_option(array('event_id' => $previous_option['event_id'], 'option_id' => $previous_option['option_id']));
        }
    }
    echo 'Les modifications ont bien été pris en compte !';
}
else
{
    set_alert_style("Erreur routing");
    add_alert('Il y a eu un problème, la variable POST n\'est même pas définie...');
}