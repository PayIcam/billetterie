<?php

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/db_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/controller_functions.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        $event = get_event_details($event_id);
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
                    set_alert_style();
                    add_error("Ce n'est pas un Icam à qui vous essayez d'ajouter des invités");
                }
            }
            else
            {
                set_alert_style();
                add_error("Les informations transmises ne correspondent pas.");
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
    set_alert_style();
    add_error("Il manque des paramètres.");
}
