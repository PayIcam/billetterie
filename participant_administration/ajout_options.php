<?php

/**
 * Controlleur de l'ajout d'options à un participant
 *
 * On laisse faire si l'event_id et les droits sont bons
 *
 * Ensuite, on initialise les variables, puis appel du template
 *
 * La validation des informations envoyées se fera en Ajax
 */

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
            $fundation_id = get_fundation_id($event_id);
            check_user_fundations_rights($fundation_id);
            if(!has_admin_rights($fundation_id, 'getPayutcClient'))
            {
                set_alert_style("Erreur droits admin");
                add_alert("Vous n'avez pas les droits nécessaires pour ajouter un participant.");
                die();
            }

            $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
            if(!empty($icam))
            {
                check_if_event_is_not_too_old(get_event_details($event_id));

                $icam = prepare_participant_displaying($icam);
                $promo_id = $icam['promo_id'];
                $site_id = $icam['site_id'];
                $options = get_optional_options(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id, 'participant_id' => $_GET['participant_id']));
                if(empty($options))
                {
                    header('Location: participants.php?event_id='.$_GET['event_id']);
                    die();
                    // set_alert_style("Erreur options déjà prises");
                    // add_alert("Le participant a déjà toutes les options possibles");
                    // die();
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

