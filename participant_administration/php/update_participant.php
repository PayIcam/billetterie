<?php

/**
 * Page appelée en Ajax lorsqu'on met à jour un participant
 *
 * Vérification simple des données transmises, et mise à jour.
 */

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/display_functions.php';
require 'requires/controller_functions.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if(!empty($_POST))
    {
        if(isset($_GET['event_id']) && isset($_GET['participant_id']))
        {
            $event_id = $_GET['event_id'];
            if(event_id_is_correct($event_id))
            {
                check_user_fundations_rights(get_fundation_id($event_id));

                $participant = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                if(!empty($participant))
                {
                    $is_icam = $participant['is_icam'];
                    $verification = check_update_participant_data($_POST, $is_icam);
                    if(!$verification)
                    {
                        die();
                    }

                    if($is_icam==0)
                    {
                        update_participant_data(array(
                            'event_id' => $event_id,
                            'participant_id' => $_GET['participant_id'],
                            'bracelet_identification' => $_POST['bracelet_identification'],
                            'nom' => $_POST['nom'],
                            'prenom' => $_POST['prenom']
                            ));
                    }
                    else
                    {
                        update_participant_data(array(
                            'event_id' => $event_id,
                            'participant_id' => $_GET['participant_id'],
                            'bracelet_identification' => $_POST['bracelet_identification']
                            ));
                    }
                    echo 'Vos modifications ont bien été ajoutées';
                }
                else
                {
                    set_alert_style("Erreur fonctionnalité");
                    add_alert("Ce participant n'existe pas.");
                }
            }
        }
        else
        {
            set_alert_style("Erreur paramètres");
            add_alert("Il manque des paramètres.");
        }
    }
    else
    {
        set_alert_style("Erreur paramètres");
        add_alert("Vous n'avez pas défini de données");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}

