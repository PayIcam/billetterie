<?php

require __DIR__ . '/../general_requires/_header.php';

if(isset($_GET['event_id']))
{
    require 'php/requires/controller_functions.php';
    require 'php/requires/db_functions.php';
    require 'php/requires/display_functions.php';

    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        $email = $_SESSION['icam_informations']->mail;
        $promo_id = $_SESSION['icam_informations']->promo_id;
        $site_id = $_SESSION['icam_informations']->site_id;

        $event = get_event_details($event_id);

        handle_pending_reservations($email, $event_id);

        if(participant_has_its_place(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id, "email" => $email)))
        {
            header('Location: edit_reservation.php?event_id='.$event_id);
            die();
        }

        check_if_event_should_be_displayed($event, $promo_id, $site_id, $email);

        $promo_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id));

        if(count($promo_specifications) > 0)
        {
            $current_participants_number = get_current_participants_number($event_id);
            $total_quota = $event['total_quota'];
            if($current_participants_number < $total_quota)
            {
                $promo_quota = $promo_specifications['quota']==null ? INF : $promo_specifications['quota'];
                if(get_current_promo_site_quota(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id)) < $promo_quota)
                {
                    $options = get_options($event_id);

                    $guests_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

                    $actual_guest_number = $promo_specifications['guest_number']>0 ? number_of_guests_to_be_displayed($promo_specifications, $guests_specifications, $current_participants_number+1, $total_quota) : 0;

                    require 'templates/formulaire_inscriptions.php';
                }
                else
                {
                    set_alert_style();
                    add_error('Toutes les places proposées à votre promo ont été vendues...');
                }
            }
            else
            {
                set_alert_style();
                add_error("Toutes les places ont été vendues...");
            }
        }
        else
        {
            set_alert_style();
            add_error("Vous n'avez pas accès à cet évènement. C'est une erreur qu'il vous soit apparu.");
        }
    }
}
else
{
    set_alert_style();
    add_error("Le GET n'est pas défini, vous n'avez pas eu la bonne url.");
}