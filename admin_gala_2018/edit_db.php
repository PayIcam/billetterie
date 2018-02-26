<?php

/**
 *
 * Cette page est appelée par edit.php lorsque il y a des données à changer dans la page d'edit.
 *
 * On ajoute donc les données à la base de données, et ensuite, on redirige vers la page d'edit.
 *
 * Soit la personne à éditer est Icam, soit elle est invitée, alors, on ajuste différentes choses dans la base de données
 *
 */


session_start();

require 'config.php';
require 'include/db_functions.php';
require 'include/display_functions.php';

$bd = connect_to_db($confSQL);

if (isset($_POST['is_icam']))
{
    $is_icam=1;
    $bracelet_id = $_POST['bracelet_id'];
    $creneau = $_POST['creneau'];
    $correct=is_correct_bracelet($bracelet_id, $creneau, $_POST['edit_id']);
    if($bracelet_id=="")
    {
        $bracelet_id=null;
    }
    if(!$correct)
    {
        $destination_id = $_POST['edit_id'];
        goto end;
    }
    if ($_POST['is_icam'] == 0)
    {
        $update_db = $bd -> prepare('UPDATE guests SET nom=:nom, prenom=:prenom, bracelet_id=:bracelet_id, plage_horaire_entrees=:creneau WHERE id=:id');
        $update_db->bindParam('id', $_POST['edit_id'], PDO::PARAM_INT);
        $update_db->bindParam('nom', $_POST['nom'], PDO::PARAM_INT);
        $update_db->bindParam('prenom', $_POST['prenom'], PDO::PARAM_STR);
        $update_db->bindParam('bracelet_id', $bracelet_id, PDO::PARAM_STR);
        $update_db->bindParam('creneau', $_POST['creneau'], PDO::PARAM_STR);
        $update_db = $update_db -> execute();

        $destination_id=set_id_inviteur($_POST['edit_id']);
    }
    elseif($_POST['is_icam'] == 1)
    {
        // $telephone = $_POST['telephone'];
        // if ($_POST['telephone'] =="")
        // {
        //     $telephone = null;
        // }
        $update_db = $bd -> prepare('UPDATE guests SET bracelet_id=:bracelet_id, plage_horaire_entrees=:creneau WHERE id=:id');
        $update_db->bindParam('id', $_POST['edit_id'], PDO::PARAM_INT);
        $update_db->bindParam('bracelet_id', $bracelet_id, PDO::PARAM_STR);
        $update_db->bindParam('creneau', $_POST['creneau'], PDO::PARAM_STR);
        $update_db = $update_db -> execute();

        $destination_id=$_POST['edit_id'];
    }
}
end:
$link = 'Location: edit.php?edit_id=' . $destination_id;
header($link);