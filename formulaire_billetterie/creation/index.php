<?php

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/display_functions.php';
require 'php/requires/db_functions.php';
require 'php/requires/controller_functions.php';

try
{
    $fundations = $payutcClient->getFundations();
}
catch(JsonClient\JsonException $e)
{
    if($e->gettype() == 'Payutc\Exception\CheckRightException')
    {
        header('Location: '.$_CONFIG['public_url']);
    }
    else
    {
        add_error("Vous n'avez vraisemblablement pas les droits, mais quelque chose d'innattendu s'est produit. Contactez Grégoire Giraud pour l'aider à résoudre ce bug svp");
        die();
    }
}

require "templates/link_to_events.php";