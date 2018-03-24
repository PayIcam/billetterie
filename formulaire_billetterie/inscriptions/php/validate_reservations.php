<?php

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/controller_functions.php';

if(isset($_GET['event_id']))
{
    if(event_id_is_correct($_GET['event_id']))
    {
        $pending_reservations = get_pending_reservations($_GET['event_id']);

        foreach($pending_reservations as $pending_reservation)
        {
            $transaction_info = $payutcClient->getTransactionInfo(array("fun_id" => $pending_reservation['fundation_id'], "tra_id" => $pending_reservation['payicam_transaction_id']));
            update_reservation_status($transaction_info->status, $pending_reservation);
        }
    }
}
else
{
    $pending_reservations = get_pending_reservations();

    foreach($pending_reservations as $pending_reservation)
    {
        $transaction_info = $payutcClient->getTransactionInfo(array("fun_id" => $pending_reservation['fundation_id'], "tra_id" => $pending_reservation['payicam_transaction_id']));
        update_reservation_status($transaction_info->status, $pending_reservation);
    }
}

// header('Location: '.$_CONFIG['public_url']);