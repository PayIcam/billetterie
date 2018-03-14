<?php

    // $email = 'antoine.giraud@2015.icam.fr';
    // $res = json_decode(file_get_contents($_CONFIG['ginger']['url'].$email."/?key=".$_CONFIG['ginger']['key']));
    // var_dump($res);
    // die();

if(isset($_GET['event_id']))
{
    require '../config.php';
    require '../general_requires/db_functions.php';
    require '../general_requires/display_functions.php';
    require 'php/requires/controller_functions.php';
    require 'php/requires/db_functions.php';
    require 'php/requires/display_functions.php';

    $db = connect_to_db($_CONFIG['ticketing']);

    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        $promo = 120;
        $site = 'Lille';
        $email = 'gregoire.giraud@2020.icam.fr';

        $promo_id = get_promo_id($promo);
        $site_id = get_site_id($site);

        $icam_event_data = get_icam_event_data(array("email" => $email, "event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id));
        if($icam_event_data=='several_emails')
        {
            echo "Plus d'un email est enregistré pour votre réservation. Contactez PayIcam pour résoudre ce problème.";
            die();
        }
        elseif(empty($icam_event_data))
        {
            echo "Vous essayez d'éditer des informations alors que vous n'avez pas de réservation.";
            header('Location: inscriptions.php?event_id='.$event_id);
            die();
        }
        $guests_event_data = get_icams_guests_data(array("event_id" => $event_id, "icam_id" => $icam_event_data['participant_id']));

        $promo_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id));

        if(count($promo_specifications) > 0)
        {
            $event = get_event_details($event_id);

            $current_participants_number = get_current_participants_number($event_id);
            $total_quota = $event['total_quota'];

            $options = get_options($event_id);

            $guests_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

            $actual_guest_number = $promo_specifications['guest_number']>0 ? number_of_guests_to_be_displayed($promo_specifications, $guests_specifications, $current_participants_number, $total_quota) : 0;
            $actual_guest_number = max(count($guests_event_data), $actual_guest_number);

            require 'templates/formulaire_inscriptions.php';
        }
        else
        {
            echo "Vous n'avez pas accès à cet évènement. C'est une erreur qu'il vous soit apparu.";
        }
    }
}
else
{
    echo "Le GET n'est pas défini, vous n'avez pas eu la bonne url.";
}

