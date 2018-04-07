<?php

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/display_functions.php';
require 'requires/controller_functions.php';

if(!empty($_POST))
{
    if(isset($_GET['event_id']))
    {
        $event_id = $_GET['event_id'];
        if(event_id_is_correct($event_id))
        {
            $ajax_json_response = array("message" => "");

            if(isset($_GET['participant_id']))
            {
                $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id']));
                if(empty($icam))
                {
                    add_error_to_ajax_response("Les informations transmises ne correspondent pas.");
                    echo json_encode($ajax_json_response);
                    die();
                }
            }
            else
            {
                var_dump($_GET);
                add_error_to_ajax_response("Il manque des paramètres.");
                echo json_encode($ajax_json_response);
                die();
            }

            $options = $_POST['options'];
            $validation = check_prepare_option_addition_data($options, $_GET['participant_id']);

            if(!$validation)
            {
                echo json_encode($ajax_json_response);
                die();
            }

            foreach($options as $option)
            {
                $option_addition_data = array(
                    "event_id" => $event_id,
                    "participant_id" => $_GET['participant_id'],
                    "option_id" => $option['option_id'],
                    "option_details" => json_encode(array("select_option" => $option['complement']))
                    );
                add_participant_option($option_addition_data);
            }

            $ajax_json_response['message'] = "L'ajout a bien été effectué";
            echo json_encode($ajax_json_response);
        }
    }
    else
    {
        set_alert_style();
        add_error("Il manque des paramètres.");
    }
}
else
{
    set_alert_style();
    add_error("Vous n'êtes pas censé appeler la page directement.");
}
