<?php

require dirname(__DIR__) . '/general_requires/_header.php';

require 'php/requires/display_functions.php';
require 'php/requires/db_functions.php';
require 'php/requires/controller_functions.php';

$student_promos = get_student_promos();
$graduated_promos = get_graduated_promos();
$sites = get_sites();

require 'templates/formulaire.php';