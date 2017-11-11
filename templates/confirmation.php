

<?php
global $bdd;

$ajout_shotgun=$bdd->prepare('INSERT INTO shotgun_desc(id_shotgun,nom_shotgun,date_debut,date_fin,descr,nb_place_tot,public_cible) 
										VALUES(DEFAULT,:nom_shotgun,:date_debut,:date_fin,:descr,:nb_place_tot,:public_cible)');

$ajout_shotgun->bindParam('nom_shotgun', $_POST['nom_shotgun'], PDO::PARAM_STR);
$ajout_shotgun->bindParam('date_debut', $_POST['date_debut']);
$ajout_shotgun->bindParam('date_fin', $_POST['date_fin']);
$ajout_shotgun->bindParam('descr', $_POST['descr'], PDO::PARAM_STR);
$ajout_shotgun->bindParam('nb_place_tot', $_POST['nb_place_tot'], PDO::PARAM_INT);
$ajout_shotgun->bindParam('public_cible', $_POST['public_cible'], PDO::PARAM_STR);

$ajout_shotgun->execute();
?>