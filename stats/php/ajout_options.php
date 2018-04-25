<?php

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/display_functions.php';
require 'requires/controller_functions.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if(!empty($_POST))
    {
        if(isset($_GET['event_id']))
        {
            $event_id = $_GET['event_id'];
            if(event_id_is_correct($event_id))
            {
                if($Auth->hasRole('admin'))
                {
                    $ajax_json_response = array("message" => "");
                    check_user_fundations_rights(get_fundation_id($event_id), false);

                    if(isset($_GET['participant_id']))
                    {
                        $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                        if(empty($icam))
                        {
                            add_alert_to_ajax_response("Les informations transmises ne correspondent pas.");
                            echo json_encode($ajax_json_response);
                            die();
                        }
                    }
                    else
                    {
                        add_alert_to_ajax_response("Il manque des paramètres.");
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    $choice_ids = $_POST['choice_ids'];
                    $choice_datas = check_prepare_option_choice_data();

                    if($choice_datas === false)
                    {
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    foreach($choice_datas as $choice_data)
                    {
                        $option_addition_data = array(
                            "event_id" => $event_id,
                            "participant_id" => $_GET['participant_id'],
                            "choice_id" => $choice_data['choice_id'],
                            "status" => "V",
                            "price" => htmlspecialchars($_POST['price']),
                            "payement" => htmlspecialchars($_POST['payement'])
                            );
                        insert_participant_option($option_addition_data);
                    }

                    $ajax_json_response['message'] = "L'ajout a bien été effectué";
                    echo json_encode($ajax_json_response);
                }
                else
                {
                    echo json_encode(array("message" => "Vous n'avez pas les droits nécessaires pour ajouter des options aux participants" , "participant_id" => ""));
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
        add_alert("Les informations en POST ne sont pas transmises");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}