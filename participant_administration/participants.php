<?php

/**
 * Controlleur de la liste des participants
 *
 * Si l'id de l'évènement est bonne, et que les droits aussi, on laisse faire
 * On récupère la liste de participants à afficher selon, la recherche, la page, et le nombre de lignes.
 * On appelle ensuite directmeent le template
 */

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/db_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/controller_functions.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        $fundation_id = get_fundation_id($event_id);
        check_user_fundations_rights($fundation_id);

        $event = get_event_details($event_id);

        $admin_rights = has_admin_rights($fundation_id, 'getPayutcClient');

        $current_page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
        $rows_per_page = (isset($_GET['rows'])) ? intval($_GET['rows']) : 25;
        $start_lign = ($current_page-1) * $rows_per_page;

        $number_participants = get_current_participants_number($event_id);

        if(isset($_POST['recherche']))
        {
            if(trim($_POST['recherche'])=='')
            {
                $number_pages = $number_participants / $rows_per_page;
                $total_number_pages = ceil($number_pages);
                $participants = get_displayed_participants($event_id, $start_lign, $rows_per_page);
            }
            else
            {
                $participants = determination_recherche($_POST['recherche'], $start_lign, $rows_per_page);
                $number_pages = $_SESSION['count_recherche'] / $rows_per_page;
                $total_number_pages = ceil($number_pages);
                unset($_SESSION['count_recherche']);
            }
        }
        else
        {
            $number_pages = $number_participants / $rows_per_page;
            $total_number_pages = ceil($number_pages);
            $participants = get_displayed_participants($event_id, $start_lign, $rows_per_page);
        }
        require 'templates/participant_list.php';
    }
}
else
{
    set_alert_style("Erreur routing");
    add_alert("event_id n'est pas défini en GET");
}
