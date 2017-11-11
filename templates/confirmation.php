

<?php
global $bdd;

$ajout_shotgun=$bdd->prepare('INSERT INTO shutgun_desc(id_shotgun,nom_shotgun,date_debut,date_fin,descr,nb_place_tot,public_cible) 
										VALUES(DEFAULT,:nom_shotgun,:date_debut,:date_fin,:descr,:nb_place_tot,:public_cible)');

$ajout_shotgun->bindParam('nom_shotgun', $nom, PDO::PARAM_STR);
$ajout_shotgun->bindParam('date_debut', $date_debut);
$ajout_shotgun->bindParam('date_fin', $date_fin);
$ajout_shotgun->bindParam('descr', $descr, PDO::PARAM_STR);
$ajout_shotgun->bindParam('nb_place_tot', $nb_place_tot, PDO::PARAM_INT);
$ajout_shotgun->bindParam('public_cible', $nom, PDO::PARAM_STR);

$ajout_shotgun->execute();
?>

<html>

	<body>
		<h1><?php var_dump($_POST) ?></h1>
	</body>
</html>

