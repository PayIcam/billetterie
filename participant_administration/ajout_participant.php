<?php

/**
 * Controlleur de l'ajout d'un participant.
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

    if(isset($_GET['event_id']))
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

            $event = get_event_details($event_id);
            check_if_event_is_not_too_old($event);

            $promos = array_column(get_event_promo_names($event_id), 'promo_name');
            $sites = array_column(get_event_site_names($event_id), 'site_name');

            if(isset($_GET['icam_id']))
            {
                $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['icam_id']));
                if(!empty($icam))
                {
                    if($icam['is_icam']==1)
                    {
                        $icam = prepare_participant_displaying($icam);
                        require 'templates/ajout_participant.php';
                    }
                    else
                    {
                        set_alert_style("Erreur fonctionnalité");
                        add_alert("Ce n'est pas un Icam à qui vous essayez d'ajouter des invités");
                    }
                }
                else
                {
                    set_alert_style("Erreur fonctionnalité");
                    add_alert("Les informations transmises ne correspondent pas.");
                }
            }
            else
            {
                require 'templates/ajout_participant.php';
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

