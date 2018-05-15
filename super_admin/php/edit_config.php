<?php

require __DIR__ . '/../../general_requires/_header.php';

require 'requires/db_functions.php';
require 'requires/display_functions.php';
require 'requires/controller_functions.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if(!empty($_POST))
    {
        if(is_correct_config_edition())
        {
            foreach($_POST as $key => $value)
            {
                update_config_availability(array('folder' => $key, 'is_active' => $value));
            }
            echo 'La mise à jour a bien été effectuée';
        }
    }
    else
    {
        set_alert_style("Erreur paramètres");
        add_alert("Vous n'avez pas défini de données");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}