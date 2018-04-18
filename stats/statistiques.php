<?php

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/db_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/controller_functions.php';

if(isset($_GET['event_id']))
{
    $event_id = $_GET['event_id'];
    if(event_id_is_correct($event_id))
    {
        check_user_fundations_rights(get_fundation_id($event_id));

        $event_details_stats = get_event_details_stats($event_id);
        $total_quota = $event_details_stats['total_quota'];
        $total_count = $event_details_stats['total_count'];
        $event_details_stats['pourcentage_inscriptions'] = $total_quota !=0 ? round(100 * $total_count / $total_quota, 2) . '%' : "undefined";
        $event_details_stats['pourcentage_student'] = $total_count !=0 ? round(100 * $event_details_stats['student_count'] / $total_count, 2) . '%' : "undefined";
        $event_details_stats['pourcentage_graduated'] = $total_count !=0 ? round(100 * $event_details_stats['graduated_count'] / $total_count, 2) . '%' : "undefined";
        $event_details_stats['pourcentage_guests'] = $total_count !=0 ? round(100 * $event_details_stats['guests_count'] / $total_count, 2) . '%' : "undefined";
        $event_details_stats['pourcentage_bracelets'] = $total_count !=0 ? round(100 * $event_details_stats['total_bracelet_count'] / $total_count, 2) . '%' : "undefined";

        $event_days_stats = get_event_days_stats($event_id);
        $event_payment_stats = get_event_payments_stats($event_id);
        $promo_specification_details_stats = get_promo_specification_details_stats($event_id);

        require 'templates/statistiques.php';
    }
}
else
{
    set_alert_style("Erreur routing");
    add_error("event_id n'est pas défini en GET");
}
