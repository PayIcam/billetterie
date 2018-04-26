<?php

require dirname(__DIR__) . '/general_requires/_header.php';

if($Auth->hasRole('admin'))
{
    require 'php/requires/db_functions.php';
    require 'php/requires/display_functions.php';
    require 'php/requires/controller_functions.php';

    if(isset($_GET['event_id']) && isset($_GET['participant_id']))
    {
        $event_id = $_GET['event_id'];
        if(event_id_is_correct($event_id))
        {
            check_user_fundations_rights(get_fundation_id($event_id));

            $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
            if(!empty($icam))
            {
                $icam = prepare_participant_displaying($icam);
                $promo_id = $icam['promo_id'];
                $site_id = $icam['site_id'];
                $options = get_optional_options(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id, 'participant_id' => $_GET['participant_id']));
                if(empty($options))
                {
                    set_alert_style("Erreur options déjà prises");
                    add_alert("Le participant a déjà toutes les options possibles");
                    die();
                }
                require 'templates/ajout_options.php';
            }
            else
            {
                set_alert_style("Erreur routing");
                add_alert("Les informations transmises ne correspondent pas.");
            }
        }
    }
    else
    {
        set_alert_style("Erreur routing");
        add_alert("Il manque des paramètres.");
    }
}
else
{
    if(isset($_GET['event_id']))
    {
        header('Location: ' . $_CONFIG["public_url"] . 'participant_administration/participants.php?event_id=' . $_GET['event_id']);
    }
    else
    {
        header('Location: ' . $_CONFIG["public_url"]);
    }
}

