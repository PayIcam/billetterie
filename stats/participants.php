<?php

require dirname(__DIR__) . '/general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/display_functions.php';
require 'requires/controller_functions.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        $event = get_event_details($event_id);

        $current_page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
        $rows_per_page = (isset($_GET['rows'])) ? intval($_GET['rows']) : 25;
        $start_lign = ($current_page-1) * $rows_per_page;

        $number_participants = get_current_participants_number($event_id);

        if(isset($_POST['recherche']))
        {
            die();
            $participants = determination_recherche($_POST['recherche'], $start_lign, $rows_per_page);
            $number_pages = $_SESSION['count_recherche'] / $rows_per_page;
            $total_number_pages = ceil($number_pages);
            unset($_SESSION['count_recherche']);
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
    set_alert_style();
    add_error("event_id n'est pas défini en GET");
}
