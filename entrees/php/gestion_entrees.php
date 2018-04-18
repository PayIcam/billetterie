<?php

require dirname(dirname(__DIR__)) . '/general_requires/_header.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if(isset($_GET))
    {
        if(isset($_GET['event_id']) && isset($_GET['action']) && isset($_GET['participant_id']))
        {
            $event_id = $_GET['event_id'];
            if(event_id_is_correct($event_id))
            {
                require 'requires/display_functions.php';
                require 'requires/db_functions.php';
                require 'requires/controller_functions.php';

                $participants = determination_recherche($_POST['recherche'], 0, 15);

                display_participants_rows($participants);
            }
        }
        else
        {
            set_alert_style('Erreur paramètres');
            add_error("Les paramètres envoyés ne sont pas bons.");
        }
    }
    else
    {
        set_alert_style('Erreur paramètres');
        add_error("Aucune donnée en GET n'est arrivée.");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_error("Vous n'êtes pas censés appeler cette page directement.");
}