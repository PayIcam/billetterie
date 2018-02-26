<h1 class="title_edit"> Editer <?php echo $participant_sous_edit['prenom']. ' '.  $participant_sous_edit['nom'] ?> </h1>

<?php
if (isset($_SESSION['erreur_bracelet']))
{
    echo '<em style="font-size:2em; text-align:center;">'. $_SESSION['erreur_bracelet'] .'</em>';
    unset($_SESSION['erreur_bracelet']);
}