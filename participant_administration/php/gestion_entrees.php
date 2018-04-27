<?php

/**
 * Page appelée en Ajax lorsqu'on demande de faire entrer ou sortir quelqu'un.
 *
 * Le paramètre action indique ce que l'on doit modifier. On fait en fonction.
 *
 * S'il y a une erreur elle est transmise, et l'action demandée ne se fait pas.
 * On renvoie également le nombre actuel d'entrées pour mettre à jour le compteur.
 */

require dirname(dirname(__DIR__)) . '/general_requires/_header.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    $ajax_json_response = array('arrival_number' => '', 'message' => '');
    if(isset($_GET))
    {
        if(isset($_GET['event_id']) && isset($_GET['action']) && isset($_GET['participant_id']))
        {
            $event_id = $_GET['event_id'];
            if(event_id_is_correct($event_id))
            {
                require 'requires/display_functions.php';
                require 'requires/db_functions.php';
                require 'requires/controller_functions.php';

                $participant = get_participant_event_arrival_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                if(!empty($participant))
                {
                    if($_GET['action'] == 'arrival')
                    {
                        if($participant['arrival']==0)
                        {
                            participant_arrives(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                            $ajax_json_response['arrival_number'] = get_arrival_number($event_id);
                            echo json_encode($ajax_json_response);
                        }
                        else
                        {
                            add_alert_to_ajax_response("Le participant est déjà rentré");
                            echo json_encode($ajax_json_response);
                        }
                    }
                    elseif($_GET['action'] == 'departure')
                    {
                        if($participant['arrival']==1)
                        {
                            participant_leaves(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                            $ajax_json_response['arrival_number'] = get_arrival_number($event_id);
                            echo json_encode($ajax_json_response);
                        }
                        else
                        {
                            add_alert_to_ajax_response("Le participant est déjà parti");
                            echo json_encode($ajax_json_response);
                        }
                    }
                    else
                    {
                        add_alert_to_ajax_response("L'action n'est pas dans la liste possible");
                        echo json_encode($ajax_json_response);
                    }
                }
                else
                {
                    add_alert_to_ajax_response("Aucun participant retrouvé avec les infos transmises");
                    echo json_encode($ajax_json_response);
                }
            }
        }
        else
        {
            add_alert_to_ajax_response("Les paramètres envoyés ne sont pas bons.");
            echo json_encode($ajax_json_response);
        }
    }
    else
    {
        add_alert_to_ajax_response("Aucune donnée en GET n'est arrivée.");
        echo json_encode($ajax_json_response);
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}