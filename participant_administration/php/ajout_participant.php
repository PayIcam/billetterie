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
                    $ajax_json_response = array("message" => "" , "participant_id" => "");
                    check_user_fundations_rights(get_fundation_id($event_id));

                    $event = get_event_details($event_id);
                    $promos = array_column(get_event_promo_names($event_id), 'promo_name');
                    $sites = array_column(get_event_site_names($event_id), 'site_name');

                    if(isset($_GET['icam_id']))
                    {
                        $icam = get_participant_event_data(array('event_id' => $event_id, 'participant_id' => $_GET['icam_id']));
                        $is_icam = 0;
                        if(!empty($icam))
                        {
                            if($icam['is_icam']==1)
                            {
                                $site = get_site_name($icam['site_id']);
                            }
                            else
                            {
                                add_alert_to_ajax_response("Ce n'est pas un Icam à qui vous essayez d'ajouter des invités");
                                die();
                            }
                        }
                        else
                        {
                            add_alert_to_ajax_response("Les informations transmises ne correspondent pas.");
                            die();
                        }
                    }
                    else
                    {
                        $is_icam = 1;
                        $site = false;
                    }

                    $validation = check_prepare_addition_data($_POST, $site);
                    $select_mandatory_options = get_select_mandatory_options(array('event_id' => $event_id, 'promo_id' => $_POST['promo_id'], 'site_id' => $_POST['site_id']));

                    if(!$validation)
                    {
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    $addition_data = array(
                        'prenom' => $_POST['prenom'],
                        'nom' => $_POST['nom'],
                        'status' => 'V',
                        'is_icam' => $is_icam,
                        'price' => $_POST['price'],
                        'payement' => $_POST['payement'],
                        'email' => $_POST['email'],
                        'telephone' => $_POST['telephone'],
                        'bracelet_identification' => $_POST['bracelet_identification'],
                        'event_id' => $event_id,
                        'site_id' => $_POST['site_id'],
                        'promo_id' => $_POST['promo_id'],
                        );

                    $participant_id = add_participant($addition_data);
                    $ajax_json_response['participant_id'] = $participant_id;
                    if(isset($_GET['icam_id']))
                    {
                        insert_icams_guest(array("event_id" => $event_id, "icam_id" => $_GET['icam_id'], "guest_id" => $participant_id));
                    }

                    foreach($select_mandatory_options as $select_mandatory_option)
                    {
                        $current_option_id = $select_mandatory_option['option_id'];
                        if(isset($previous_option_id))
                        {
                            if($current_option_id==$previous_option_id)
                            {
                                continue;
                            }
                        }

                        $promo_site_has_options_data = array(
                            "event_id" => $event_id ,
                            "participant_id" => $participant_id ,
                            "choice_id" => $select_mandatory_option['choice_id'] ,
                            "status" => "V",
                            "price" => $select_mandatory_option['price'],
                            "payement" => $_POST['payement']
                            );
                        insert_participant_option($promo_site_has_options_data);

                        $previous_option_id=$current_option_id;
                    }

                    $ajax_json_response['message'] = "L'ajout a bien été effectué";
                    echo json_encode($ajax_json_response);
                }
                else
                {
                    echo json_encode(array("message" => "Vous n'avez pas les droits nécessaires pour ajouter des participants" , "participant_id" => ""));
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
        set_alert_style("Erreur routing");
        add_alert("Aucune information transmise en POST n'est passée.");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}

