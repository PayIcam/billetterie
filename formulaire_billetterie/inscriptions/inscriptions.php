<?php

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

        $promo_id = get_promo_id($promo);
        $site_id = get_site_id($site);

        $promo_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id));

        if(count($promo_specifications) > 0)
        {
            $event = get_event_details($event_id);

            $current_participants_number = get_current_participants_number($event_id);
            $total_quota = $event['total_quota'];
            if($current_participants_number < $total_quota)
            {
                $options = get_options($event_id);
                require 'templates/formulaire_inscriptions.php';
            }
            else
            {
                echo "Toutes les places ont été vendues...";
            }
        }
        else
        {
            echo "Vous n'avez pas accès à cet évènement. C'est une erreur qu'il vous soit apparu.";
        }
    }
    else
    {
        echo "Aucun évènement ne correspond au Get spécifié.";
    }
}
else
{
    echo "Le GET n'est pas défini, vous n'avez pas eu la bonne url.";
}