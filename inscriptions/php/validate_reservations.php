<?php

/**
 * Page appelée en callback de la validation d'une transaction.
 * Au moment de payer, si la personne paye bien, on peux récupérer le statut de la transaction et voir que c'est bien le cas. Il faut alors mettre à jour les tables de ticketing.
 * Cette page sert donc à actualiser le statut des transactions en attente, mais ayant payé (et donc des lignes dans participants & participant_has_options associées)
 */

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/controller_functions.php';

//Si on précise un event_id, on le fait juste pour cet event, sinon, on le fait pour tous
if(isset($_GET['event_id']))
{
    if(event_id_is_correct($_GET['event_id']))
    {
        $pending_reservations = get_pending_reservations($_GET['event_id']);
        foreach($pending_reservations as $pending_reservation)
        {
            $transaction_info = $payutcClient->getTransactionInfo(array("fun_id" => $pending_reservation['fundation_id'], "tra_id" => $pending_reservation['payicam_transaction_id']));
            if($transaction_info->status != "W") {
                update_reservation_status($transaction_info->status, $pending_reservation);
            } else {
                date_default_timezone_set('Europe/Paris');
                $now = new \DateTime();
                $creation = new \DateTime($pending_reservation['date_demande']);
                $difference = $now->diff($creation);
                $old_creation = ($difference->days>=1 || $difference->h>=1 || $difference->m>15);
                if($old_creation) {
                    update_reservation_status("A", $pending_reservation);
                }
        }
    }
}
else
{
    $pending_reservations = get_pending_reservations();
    foreach($pending_reservations as $pending_reservation)
    {
        $transaction_info = $payutcClient->getTransactionInfo(array("fun_id" => $pending_reservation['fundation_id'], "tra_id" => $pending_reservation['payicam_transaction_id']));
        if($transaction_info->status != "W")
        {
            update_reservation_status($transaction_info->status, $pending_reservation);
        } else {
            date_default_timezone_set('Europe/Paris');
            $now = new \DateTime();
            $creation = new \DateTime($pending_reservation['date_demande']);
            $difference = $now->diff($creation);
            $old_creation = ($difference->days>=1 || $difference->h>=1 || $difference->m>15);
            if($old_creation) {
                update_reservation_status("A", $pending_reservation);
            }
        }
    }
}

header('Location: ' . $_CONFIG['public_url']);