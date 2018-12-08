<?php

/**
 * Page appelée en Ajax lorsqu'on demande à créer la page d'ajout de participants. On affiche toutes les options obligatoires dans un select chacun
 */

require dirname(dirname(__DIR__)) . '/general_requires/_header.php';

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
    die();
}

if(isset($_GET))
{
    if(isset($_GET['event_id']))
    {
        $event_id = $_GET['event_id']*1;
        if(event_id_is_correct($event_id))
        {
            if(isset($_GET['promo']) && isset($_GET['site']))
            {
                require 'requires/display_functions.php';
                require 'requires/db_functions.php';
                require 'requires/controller_functions.php';

                $promo_id = get_promo_id($_GET['promo']);
                $site_id = get_site_id($_GET['site']);

                $mandatory_options = get_select_mandatory_options(array('event_id' => $_GET['event_id'], 'promo_id' => $promo_id, 'site_id' => $site_id));
                if(!empty($mandatory_options)) {
                    require '../templates/mandatory_options_selects.php';
                }
            }
            else
            {
                set_alert_style('Erreur routing');
                add_alert("La promo n'est pas définie.");
            }
        }
    }
    else
    {
        set_alert_style('Erreur paramètres');
        add_alert("Vous n'avez pas spécifié pour quel évènement vous vouliez administrer les entrées.");
    }
}
else
{
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}