<?php

require '../include/html/add/header_add.php';

if(isset($_SESSION['erreur_bracelet']))
{
    echo '<span style="font-size:2em; text-align: center;">'.$_SESSION['erreur_bracelet'].'</span>';
    unset($_SESSION['erreur_bracelet']);
}

if(isset($_GET['add_id']))
{
    $add_id = $_GET['add_id'];
    $add_data = select_single_lign($add_id);
    $nombre_invites_et_total = nombre_invites($add_data);
    $add_data['nombre_invites_et_total'] = $nombre_invites_et_total;

    require '../include/html/add/tableau.php';
    require '../include/html/add/add_form.php';
}
else
{
    require '../include/html/add/add_form.php';
}
require '../include/html/footer.php';
