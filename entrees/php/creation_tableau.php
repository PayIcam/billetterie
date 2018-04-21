<?php

require dirname(dirname(__DIR__)) . '/general_requires/_header.php';

if(isset($_POST))
{
    if(isset($_GET['event_id']))
    {
        $event_id = $_GET['event_id'];
        if(event_id_is_correct($event_id))
        {
            if(isset($_POST['recherche']))
            {
                require 'requires/display_functions.php';
                require 'requires/db_functions.php';
                require 'requires/controller_functions.php';

                $participants = trim($_POST['recherche'])!='' ? determination_recherche($_POST['recherche'], 0, 15) : get_displayed_participants($event_id, 0, 25);

                display_participants_rows($participants);
            }
            else
            {
                set_alert_style('Erreur routing');
                add_error("La recherche n'est pas définie.");
            }
        }
    }
    else
    {
        set_alert_style('Erreur paramètres');
        add_error("Vous n'avez pas spécifié pour quel évènement vous vouliez administrer les entrées.");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_error("Vous n'êtes pas censés appeler cette page directement.");
}