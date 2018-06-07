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

    // Avant de faire quelque traitement qu'il soit, il faut vérifier tout le formulaire.
    // S'il y a la moindre erreur, le script s'arrète, et une erreur est affichée indiquant l'origine du problème.
    // Ce n'est qu'une ligne sur cette page, mais en vrai il y en a 700, pas de panique, c'est secure
    // Tous les champs sont vérifiés, passés en htmlspecialchars, et à la moindre chose suspecte, le code s'arrète, et une erreur est soulevée
    check_and_prepare_data();

    // Il faut créer tout d'abord les catégories dans lesquelles ranger nos articles pour l'event.
    $scoobydoo_event_id = $payutcClient->setCategory(array("name" => $event->name, "service" => 'Billetterie', "parent_id" => null, "fun_id" => $event->fundation_id))->success;
    $scoobydoo_promos_id = $payutcClient->setCategory(array("name" => 'Prix par promo', "service" => 'Billetterie', "parent_id" => $scoobydoo_event_id, "fun_id" => $event->fundation_id))->success;
    $scoobydoo_options_id = $payutcClient->setCategory(array("name" => 'Options', "service" => 'Billetterie', "parent_id" => $scoobydoo_event_id, "fun_id" => $event->fundation_id))->success;

    $scoobydoo_category_ids = json_encode(array("scoobydoo_event_id" => $scoobydoo_event_id, "scoobydoo_promos_id" => $scoobydoo_promos_id, "scoobydoo_options_id" => $scoobydoo_options_id));

    //On prépare alors notre insertion de données pour la table events, pas besoin de vérifier quoi que ce soit, c'est déjà fait !
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

    //On insère toutes les promos qui ont accès à l'event avec leurs particularités
    foreach($event_promos as $promo_data)
    {
        //Il faut créer un article PayIcam pour chaque promo. C'est cet article que les participants vont payer, selon leur promo.
        $scoobydoo_article_id = $payutcClient->setProduct(array(
            "name" => $event->name . " Prix " . $promo_data->promo . " " . $promo_data->site,
            "service" => 'Billetterie',
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

    //On insère ensute toutes les options
    foreach($options as $option)
    {
        $table_option_data = array(
            "name" => $option->name,
            "description" => $option->description,
            "is_active" => $option->is_active,
            "is_mandatory" => $option->is_mandatory,
            "type" => $option->type,
            "quota" => $option->quota,
            "event_id" => $event_id
            );
        $option_id = insert_option($table_option_data);

        if($option->type == "Checkbox")
        {
            //On crée un article pour l'option aussi
            $scoobydoo_article_id = $payutcClient->setProduct(array(
                "name" => $event->name . " Option " . $option->name,
                "service" => "Billetterie",
                "parent" => $scoobydoo_options_id,
                "prix" => 100*$option->type_specification->price,
                "stock" => $option->quota,
                "image" => '',
                "alcool" => 0,
                "cotisant" => false,
                "fun_id" => $event->fundation_id
                ))->success;

            $option_choices = array(
                'price' => $option->type_specification->price,
                'scoobydoo_article_id' => $scoobydoo_article_id,
                'option_id' => $option_id
                );
        }
        elseif($option->type == "Select")
        {
            $option_choices = array();
            foreach($option->type_specification as &$select_option)
            {
                //On en crée plusieurs forcément si c'est un select, autant qu'il y a de sous-options
                $scoobydoo_article_id = $payutcClient->setProduct(array(
                    "name" => $event->name . " Option " . $option->name . " Choix " . $select_option->name,
                    "service" => 'Billetterie',
                    "parent" => $scoobydoo_options_id,
                    "prix" => 100*$select_option->price,
                    "stock" => $select_option->quota,
                    "image" => '',
                    "alcool" => 0,
                    "cotisant" => false,
                    "fun_id" => $event->fundation_id
                    ))->success;

                $option_choices[] = array(
                    'price' => $select_option->price,
                    'scoobydoo_article_id' => $scoobydoo_article_id,
                    'name' => $select_option->name,
                    'quota' => $select_option->quota,
                    'is_removed' => 0,
                    'option_id' => $option_id
                    );
            }
        }
        //On insère dans la table option_choices ces infos
        insert_option_choices($option_choices, $option->type);

        //Et il ne faut pas oublier d'ajouter les infos sur les promos qui ont accès à l'option.
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
    add_alert("Vous n'êtes pas censé ouvrir cette page directement.");
}