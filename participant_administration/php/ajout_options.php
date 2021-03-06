<?php

/**
 * Page appelée en Ajax lorsqu'on ajoute des options à un participant
 * Vérifications basiques des données, puis ajout simple des options au participant
 */

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

                    $fundation_id = get_fundation_id($event_id);
                    $error = check_user_fundations_rights($fundation_id);
                    check_if_event_is_not_too_old(get_event_details($event_id));

                    if(!has_admin_rights($fundation_id, 'getPayutcClient'))
                    {
                        add_alert_to_ajax_response("Vous n'avez pas les droits nécessaires pour ajouter un participant.");
                        echo json_encode($ajax_json_response);
                        die();
                    }

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

                    if(!empty($_POST['choice_ids'])) {
                        $choice_ids = $_POST['choice_ids'];

                        $choice_datas = check_prepare_option_choice_data();
                        if($choice_datas === false)
                        {
                            echo json_encode($ajax_json_response);
                            die();
                        }
                        $sum_prices = array_sum(array_column($choice_datas, 'price'));

                        foreach($choice_datas as $choice_data)
                        {
                            $price = $sum_prices == 0 ? 0 : round($_POST['price'] * $choice_data['price'] / $sum_prices, 2);
                            $option_data = array(
                                "event_id" => $event_id,
                                "participant_id" => $_GET['participant_id'],
                                "choice_id" => $choice_data['choice_id'],
                                "status" => "V",
                                "price" => $price,
                                "payement" => $_POST['payement']
                                );

                            $previous_status = get_participant_previous_option_choice_status(array('event_id' => $event_id, 'participant_id' => $_GET['participant_id'], 'choice_id' => $choice_data['choice_id']));
                            if($previous_status!==false) {
                                update_cancelled_option($option_data);
                            } else {
                                insert_participant_option($option_data);
                            }
                        }
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