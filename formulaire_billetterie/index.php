<?php

require 'config.php';
require 'php/php_functions.php';

$db = connect_to_db($_CONFIG['ticketing']);

$student_promos = get_student_promos();
$graduated_promos = get_graduated_promos();
$sites = get_sites();

require 'html/formulaire.php';