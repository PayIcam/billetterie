<?php

session_start();

$edit_id = $_GET['edit_id'];

require 'config.php';
require 'include/db_functions.php';
require 'include/display_functions.php';
require 'include/html/display_html_functions.php';

$bd = connect_to_db($confSQL);

$participant_sous_edit = select_single_lign($edit_id);

$nombre_invites_et_total = nombre_invites($participant_sous_edit);
$participant_sous_edit['nombre_invites_et_total'] = $nombre_invites_et_total;

if ($participant_sous_edit['is_icam'] ==1)
{
    if ($nombre_invites_et_total[0] >0)
    {
        $invites_rattaches = set_invites($edit_id);
    }
}
else
{
    $id_inviteur = set_id_inviteur($edit_id);
    $participant_inviteur = select_single_lign($id_inviteur);
    $nombre_invites_et_total_inviteur = nombre_invites($participant_inviteur);
    $participant_inviteur['nombre_invites_et_total']=$nombre_invites_et_total_inviteur;
}

$current_creneaux_quotas = count_creneaux_quotas();

require 'include/html/header.php';
require 'include/html/edit/edit_title.php';
require 'include/html/edit/tableau_edit.php';
require 'include/html/edit/form_edit.php';
require 'include/html/edit/tableau_complementaire.php';
?>
<script src='js/check_edit_form.js'> </script>
<?php
include 'include/html/footer.php';
