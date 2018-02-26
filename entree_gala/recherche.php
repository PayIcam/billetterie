<?php

/////////////////////////////////////////////////////////////////////////////////////////////////
//   Page appelée pour la recherche, elle génère un fichier xml contenant toutes les données   //
/////////////////////////////////////////////////////////////////////////////////////////////////

session_start();

// On défini les noms des mois de l'année
$mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

// Requis pour générer un fichier xml
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

// racine qui contient les données
echo "<root>";

// on récupère les données pour la connection à la bdd
require('data/config.php');
require ('include/db_functions.php');

// connection à la bdd
$connexion = connect_to_db();

// on récupère la chaine de caractère à rechercher
$recherche = $_GET["recherche"];

// envoie de la requête
$invite = determination_recherche($recherche, 0);

// on mets dans le xml le compteur de résultats de la recherche et celui du nombre d'entrées réalisées
echo "<nb_result name='affichage de " . count($invite) . "/" . $_SESSION['count_recherche'] . " résultats' />";
echo "<nb_entrees name='" . nb_entrees() . "/" . nb_participants() . " entrées' />";

// on crée pour chaque invité un élément row qui contient toutes les données
for ($i = 0; $i < count($invite); $i++) {
	echo "<row ";
    echo 'id="' . $invite[$i]["id"] . '" ';
    echo 'bracelet="' . $invite[$i]["bracelet_id"] . '" ';
    echo 'prenom="' . $invite[$i]["prenom"] . '" ';
    echo 'nom="' . $invite[$i]["nom"] . '" ';
    echo 'tickets_boisson="' . $invite[$i]["tickets_boisson"] . '" ';
    echo 'promo="' . $invite[$i]["promo"] . '" ';
    echo 'creneau="' . $invite[$i]["plage_horaire_entrees"] . '" ';

    echo 'nb_invites="' . nombre_invites($invite[$i]["id"]) . '" ';

    echo 'inscription="' . $invite[$i]["DAY(inscription)"] . " " . $mois[$invite[$i]["MONTH(inscription)"]] . '" ';
    echo 'est_arrive="' . has_arrived($invite[$i]["id"]) . '" ';


    // Si c'est un icam, on récupère le nom de ses invités
    if(is_icam($invite[$i]["id"])){
        $invites = get_guests($invite[$i]["id"]);
        echo "table_title='Liste de ses invités :' ";
        echo "invites='";
        for ($j = 0; $j < count($invites); $j++) {
            echo $invites[$j]["prenom"] . ":" . $invites[$j]["nom"] . ";";
        }
        echo "' ";

    // Si ce n'est pas un icam, on récupère le nom de la personne qui l'a invité
    }else{
        $invites = get_inviter($invite[$i]["id"]);
        echo "table_title='A été invité par :' ";
        if(count($invites)!=0){
            echo "invites='" . $invites[0]["prenom"] . ":" . $invites[0]["nom"] . ";' ";
        }else{
            echo "invites='inconnu:;' ";
        }
        
    }

    // vérification pour le diner et la conférence
    if (repas($invite[$i]["id"]) ==1 or buffet($invite[$i]["id"])==1) {
        if (repas($invite[$i]["id"]) ==1 and buffet($invite[$i]["id"]) ==1) {
        echo "repas='Dîner et conférence' ";
        } elseif (repas($invite[$i]["id"]) ==1 and !buffet($invite[$i]["id"]) ==1) {
        echo "repas='Dîner' ";
        } else {
        echo "repas='Conférence' ";
        }
    } else {
        echo "repas='Pas d`options' ";
    }

    echo "/>";
}

echo "</root>";
?>