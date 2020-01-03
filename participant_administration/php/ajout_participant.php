<?php

/**
 * Page appelée en Ajax lorsqu'on ajoute des participants
 * Vérifications basiques des données, puis ajout simple des options au participant
 *
 * Si l'id d'un icam est précisée en Get, c'est qu'il faut lui ajouter le participant à ses invités
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
                    $ajax_json_response = array("message" => "" , "participant_id" => "");

                    $fundation_id = get_fundation_id($event_id);
                    check_user_fundations_rights($fundation_id);
                    check_if_event_is_not_too_old(get_event_details($event_id));

                    if(!has_admin_rights($fundation_id, 'getPayutcClient'))
                    {
                        add_alert_to_ajax_response("Vous n'avez pas les droits nécessaires pour ajouter un participant.");
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    $event = get_event_details($event_id);
                    $promos = array_column(get_event_promo_names($event_id), 'promo_name');
                    $sites = array_column(get_event_site_names($event_id), 'site_name');

                    if(isset($_GET['icam_id']))//On ajoute des invités à un Icam.
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
                    else//On ajout un participant "Icam"
                    {
                        $is_icam = 1;
                        $site = false;
                    }

                    $validation = check_prepare_addition_data($site);
                    if(!$validation)
                    {
                        echo json_encode($ajax_json_response);
                        die();
                    }

                    if (!empty($_POST['choice_ids'])) {
                        $choice_datas = check_prepare_option_choice_data(false);

                        if($choice_datas === false)
                        {
                            echo json_encode($ajax_json_response);
                            die();
                        }
                    } else {
                        $choice_datas = [];
                    }

                    /**
                     * Il n'y a qu'un seul input pour le prix de la place de base + les options obligatoires. On veut déterminer comment répartir ce prix.
                     *
                     * Si le prix donné est nul, quoi qu'il arrive, tout vaudra 0.
                     * Si une option (obligatoire) est gratuite, son prix vaudra 0.
                     * Si le prix de l'évènement est nul, il vaudra 0, sauf si toutes les options sont gratuites. Alors, il vaudra le prix donné par l'utilisateur.
                     * Sinon, le prix des options payantes, et de la place sont déterminés au prorata de leur prix défini dans l'event.
                     *
                     * Ainsi, si la place est à 15€ & les options à 5€ (0 + 2 + 3), et que l'utilisateur indique 10€ de payés,
                     * la place vaudra 15/20 *10 = 7,5€ // l'option gratuite vaudra 0€, l'option à 2€ 1€, l'option à 3€ 1,5€
                     */
                    $basic_price = get_event_price(array('event_id' => $_GET['event_id'], 'promo_id' => $_POST['promo_id'], 'site_id' => $_POST['site_id']));
                    $option_prices = array_column($choice_datas, 'price');
                    if(!empty($option_prices)) {
                        $option_prices_sum = array_sum($option_prices);
                        $options_nb = count($option_prices);
                        $distinct_prices = array_unique($option_prices);
                        $paid_options_nb = count($distinct_prices);
                        if($basic_price == 0) {
                            if($distinct_prices === [0]) {
                                $ticket_price = $_POST['price'];
                            } else {
                                $option_price = $_POST['price'];
                                $ticket_price = 0;
                            }
                        } else {
                            if($distinct_prices === [0]) {
                                $ticket_price = $_POST['price'];
                            } else {
                                if($basic_price + $option_prices_sum==0) {
                                    $ticket_price_percentage = 0;
                                } else {
                                    $ticket_price_percentage = $basic_price / ($basic_price + $option_prices_sum);
                                    $ticket_price = round($_POST['price'] * $ticket_price_percentage, 2);
                                    $option_price = $_POST['price'] - $ticket_price;
                                }
                            }
                        }
                    } else {
                        $ticket_price = $_POST['price'];
                    }

                    $addition_data = array(
                        'prenom' => $_POST['prenom'],
                        'nom' => $_POST['nom'],
                        'status' => 'V',
                        'is_icam' => $is_icam,
                        'price' => $ticket_price,
                        'payement' => $_POST['payement'],
                        'email' => $_POST['email'],
                        'bracelet_identification' => $_POST['bracelet_identification'],
                        'event_id' => $event_id,
                        'site_id' => $_POST['site_id'],
                        'promo_id' => $_POST['promo_id'],
                        );

                    $participant_id = add_participant($addition_data);
                    $ajax_json_response['participant_id'] = $participant_id;

                    foreach($choice_datas as $choice_data)
                    {
                        if($_POST['price'] ==0) {
                            $option_price = 0;
                        } else {
                            if($option_prices_sum==0) {
                                $option_price = 0;
                            } else {
                                $option_price = round($option_price * $choice_data['price'] / $option_prices_sum, 2);
                            }
                        }

                        $option_addition_data = array(
                            "event_id" => $event_id,
                            "participant_id" => $participant_id,
                            "choice_id" => $choice_data['choice_id'],
                            "status" => "V",
                            "price" => $option_price,
                            "payement" => htmlspecialchars($_POST['payement'])
                            );
                        insert_participant_option($option_addition_data);
                    }

                    if(isset($_GET['icam_id']))
                    {
                        insert_icams_guest(array("event_id" => $event_id, "icam_id" => $_GET['icam_id'], "guest_id" => $participant_id));
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

