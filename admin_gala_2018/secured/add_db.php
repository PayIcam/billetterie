<?php

session_start();

require '../include/display_functions.php';
require '../config.php';
require '../include/db_functions.php';

$bd = connect_to_db($confSQL);

$bracelet = $_POST['bracelet'];
$creneau = $_POST['creneau'];

$correct = is_correct_bracelet($bracelet, $creneau, 0);
if(!$correct)
{
    $_SESSION['erreur_ajout']=$_POST;

    $link = (isset($_POST['id_icam_invitant'])) ? 'Location: ajouter_invite.php?add_id=' . $_POST['id_icam_invitant'] : 'Location: ajouter_invite.php';

    goto end;
}

$buffet = (isset($_POST['conference'])) ? 1 : 0;
$repas = (isset($_POST['dîner'])) ? 1 : 0;

if(!isset($_POST['invite']))
{

    $tel = $_POST['tel'];
    $email = $_POST['email'];
    if($tel=="")
    {
        $tel=null;
    }
    if($email=="")
    {
        $email=null;
    }
    $insert = $bd->prepare('INSERT INTO guests(nom, prenom, repas, buffet, is_icam, promo, email, telephone, inscription, bracelet_id, plage_horaire_entrees, paiement, price, tickets_boisson) VALUES (:nom, :prenom, :repas, :buffet, :is_icam, :promo, :email, :telephone, CURRENT_TIMESTAMP(), :bracelet_id, :plage_horaire_entrees, :paiement, :price, :tickets_boisson)');
    $insert ->execute(array(
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'repas' => $repas,
        'buffet' => $buffet,
        'is_icam' => $_POST['is_icam'],
        'promo' => $_POST['promo'],
        'email' => $email,
        'telephone' => $tel,
        'bracelet_id' => $bracelet,
        'plage_horaire_entrees' => $creneau,
        'paiement' => $_POST['paiement'],
        'price' => $_POST['price'],
        'tickets_boisson' => $_POST['tickets']
        ));
    $link = 'Location: ajouter_invite.php';
}
else
{
    if($_POST['promo']=="")
    {
        $_POST['promo']=null;
    }
    if($creneau=='')
    {
        $creneau=null;
    }

    $insert = $bd->prepare('INSERT INTO guests(nom, prenom, repas, buffet, is_icam, promo, inscription, bracelet_id, plage_horaire_entrees, paiement, price, tickets_boisson) VALUES (:nom, :prenom, :repas, :buffet, :is_icam, :promo, CURRENT_TIMESTAMP(), :bracelet_id, :plage_horaire_entrees, :paiement, :price, :tickets_boisson)');
    $insert ->execute(array(
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'repas' => $repas,
        'buffet' => $buffet,
        'is_icam' => $_POST['is_icam'],
        'promo' => $_POST['promo'],
        'bracelet_id' => $bracelet,
        'plage_horaire_entrees' => $creneau,
        'paiement' => $_POST['paiement'],
        'price' => $_POST['price'],
        'tickets_boisson' => $_POST['tickets']
        ));

    $id_invite = $bd->lastInsertId();
    $link_invite = $bd->prepare('INSERT INTO icam_has_guest VALUES(:icam_id, :guest_id)');
    $link_invite->execute(array('icam_id' => $_POST['add_id'], 'guest_id' => $id_invite));

    $link='Location: ajouter_invite.php?add_id=' . $_POST['id_icam_invitant'];
}

end:
header($link);