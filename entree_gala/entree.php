<?php

//////////////////////////////////////////////////////
//   Page appelée lors de l'appui sur 'est entré'   //
//////////////////////////////////////////////////////

session_start();

require('data/config.php');
require ('include/db_functions.php');

// connection à la bdd
$connexion = connect_to_db();

// on récupère les données de l'url
$id = $_GET["id"];
$arrived = $_GET["arrived"];

// on envoie la requête
set_has_arrived($id, $arrived);

?>