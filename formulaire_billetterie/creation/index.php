<?php

require '../config.php';
require '../general_requires/db_functions.php';
require '../general_requires/display_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/db_functions.php';
require 'php/requires/controller_functions.php';

$db = connect_to_db($_CONFIG['ticketing']);

$student_promos = get_student_promos();
$graduated_promos = get_graduated_promos();
$sites = get_sites();

require 'templates/formulaire.php';