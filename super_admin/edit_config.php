<?php

require __DIR__ . '/../general_requires/_header.php';

if(isset($_SESSION['REQUEST_URI']))
{
    header('Location: '. $_SESSION['REQUEST_URI']);
    unset($_SESSION['REQUEST_URI']);
    die();
}

require 'php/requires/db_functions.php';
require 'php/requires/display_functions.php';
require 'php/requires/controller_functions.php';

$config = get_config();

require 'templates/edit_config.php';