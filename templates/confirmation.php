

<?php
//valeures de test
$_POST['nom_shotgun'] = "magrite";
$_POST['date_debut'] = "2017-10-10 00:00:00";
$_POST['date_fin'] = "2017-10-11 00:00:00";
$_POST['descr'] = "test";
$_POST['nb_place_tot'] = "100";
$_POST['public_cible'] = "119";
$_POST['nom_option1'] = 'option1';
$_POST['prix_option1'] = '15';
$_POST['nb_place_option1'] = '10';
$_POST['nom_option2'] = 'option2';
$_POST['prix_option2'] = '20';
$_POST['nb_place_option2'] = '30';


// on importe l'objet PDO crée dans les dépendance
global $bdd;

//on prepare la requete pour enregistrer le shotgun
$ajout_shotgun=$bdd->prepare('INSERT INTO shotgun_desc(id_shotgun,nom_shotgun,date_debut,date_fin,descr,nb_place_tot,public_cible) 
										VALUES(DEFAULT,:nom_shotgun,:date_debut,:date_fin,:descr,:nb_place_tot,:public_cible)');

$ajout_shotgun->bindParam('nom_shotgun', $_POST['nom_shotgun'], PDO::PARAM_STR);
$ajout_shotgun->bindParam('date_debut', $_POST['date_debut']);
$ajout_shotgun->bindParam('date_fin', $_POST['date_fin']);
$ajout_shotgun->bindParam('descr', $_POST['descr'], PDO::PARAM_STR);
$ajout_shotgun->bindParam('nb_place_tot', $_POST['nb_place_tot'], PDO::PARAM_INT);
$ajout_shotgun->bindParam('public_cible', $_POST['public_cible'], PDO::PARAM_STR);

$ajout_shotgun->execute();

//on veux obtenir une liste des options

//on separe dc dans un 1er temps les champs qui comportent le nom 'option'
$info_option = array();
foreach ($_POST as $name => $value)
{
	if (preg_match("[option]", $name)) 
	{
		//on selectionne le nombre compris dans leur clef si il existe (pour pouvoir trier les champs en fct de leur numéro d'option)
		preg_match("#[0-9]#", $name, $rang);
		//le rang correspond au numéro d'option
		$rang=$rang[0];

		if (!is_null($rang))
		{
			//on vérifie que les clefs des champs correspond bien et on les ranges
			if(preg_match("[prix]", $name) || preg_match("[nb_place]", $name) || preg_match("[nom]", $name))
			{
				$info_option[$rang][$name]=$value;
			}
			else
			{
				throw Exception("post incorrect");
			}
		}
		else
		{
			throw Exception("post incorrect");
		}
	}	
}
// On obtien une variable du type :
// array (size=2)
//   1 => 
//     array (size=3)
//       'nom_option1' => string 'option1' (length=7)
//       'prix_option1' => string '15' (length=2)
//       'nb_place_option1' => string '10' (length=2)
//   2 => 
//     array (size=3)
//       'nom_option2' => string 'option2' (length=7)
//       'prix_option2' => string '20' (length=2)
//       'nb_place_option2' => string '30' (length=2)

// on veut ensuite sauvegarder ces données
// on recupère l'id du shotgun que l'on vient de creer
$id_shotgun=$bdd->lastInsertId();
foreach($info_option as $rang => $list_enreg)
{
	//on selection les sous-listes de 3 informations
	if (count($list_enreg)==3)
	{
		//on sauvegarde
		$ajout_option=$bdd->prepare('INSERT INTO choix	(id_opt,id_shotgun,nom,prix,nb_dispo_tot,nb_choisi)
												VALUES 	(DEFAULT,:id_shotgun,:nom,:prix,:nb_dispo_tot,0)');
		$ajout_option->bindParam('id_shotgun',$id_shotgun, PDO::PARAM_INT);
		//on selectionne l'info dont la clef comporte 'nom'
		$liste_nom=preg_grep('#nom#',array_keys($list_enreg));
		$ajout_option->bindParam('nom',$list_enreg[reset($liste_nom)], PDO::PARAM_STR);

		$liste_prix=preg_grep('#prix#',array_keys($list_enreg));
		$ajout_option->bindParam('prix',$list_enreg[reset($liste_prix)], PDO::PARAM_INT);
		
		$liste_nb_place=preg_grep('#nb_place#',array_keys($list_enreg));
		$ajout_option->bindParam('nb_dispo_tot',$list_enreg[reset($liste_nb_place)], PDO::PARAM_INT);

		$ajout_option->execute();

	}
	else
	{
		throw Exception('il manque des elements dans le post');
	}
}

?>