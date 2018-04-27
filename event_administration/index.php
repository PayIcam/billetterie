<?php

/**
 * Controlleur de la page servant juste à afficher les liens vers les différentes billetteries disponibles. On peux aussi choisir d'en ajouter une.
 */

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/display_functions.php';
require 'php/requires/db_functions.php';
require 'php/requires/controller_functions.php';

require "templates/link_to_events.php";