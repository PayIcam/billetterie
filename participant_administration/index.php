<?php

/**
 * Controlleur très basique pour la page d'accueil de la partie administration des participants (est affiché sur la page la liste des events possibles)
 */

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/display_functions.php';
require 'php/requires/db_functions.php';
require 'php/requires/controller_functions.php';

require "templates/link_to_participant_lists.php";