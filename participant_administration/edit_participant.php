<?php

/**
 * Controlleur de l'edition d'un participant.
 *
 * On laisse faire si l'event_id et les droits sont bons
 *
 * Ensuite, on initialise les variables, puis appel du template
 *
 * La validation des informations envoyées se fera en Ajax
 */

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/db_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/controller_functions.php';

if(isset($_GET['event_id']) && isset($_GET['participant_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        check_user_fundations_rights(get_fundation_id($event_id));

        $participant = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
        if(!empty($participant))
        {
            $event = get_event_details($event_id);
            check_if_event_is_not_too_old($event);

            $participant = prepare_participant_displaying($participant);
            if($participant['is_icam']==1)
            {
                $specification = 'link_invite';
                $participants_complementaires = get_icams_guests_info(array('event_id' => $event_id, 'icam_id' => $participant['participant_id']));
            }
            else
            {
                $specification = 'link_icam';
                $participants_complementaires = get_guests_icam_inviter_info(array('event_id' => $event_id, 'guest_id' => $participant['participant_id']));
            }
            require 'templates/edit_participant.php';
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
