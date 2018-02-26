<?php
/**
 *
 * Cette page consiste en l'affichage des données de la page d'accueil du site. Elle est appelée par la page index.php
 *
 * Le header y est déjà appelé.
 *
 * Ensuite, on affiche tout le contenu de la page.
 *
 * On affiche donc tout d'abord une présentation de la page avec des informations générales
 * On affiche ensuite les boutons permettant de changer de page et de nombre de lignes par page
 *
 * Ensuite, on affiche le tableau des inscris.
 *
 * Puis on réaffiche les boutons permettant de changer de page
 *
 * Le footer est appelé dans la page index.php juste après.
 *
 */
?>

<div class = 'container all_index'>

    <?php
    require 'include/html/index/index_title_search.php';

    if (isset($_SESSION['search_match'])) {echo htmlspecialchars($_SESSION['search_match']); unset($_SESSION['search_match']);}

    change_number_rows($rows_per_page);
    ?>

    <div class="change_page">
        <?php change_pages($current_page, $rows_per_page, $total_number_pages); ?>
    </div>

    <section class="row" id="tableau">
        <table class="table table-striped">
            <thead>
                <?php display_liste_head(); ?>
            </thead>
            <tbody>
                <?php
                foreach ($invite as $participant)
                {
                    $nombre_invites_et_total = nombre_invites($participant);
                    $participant['nombre_invites_et_total'] = $nombre_invites_et_total;
                    display_participant_info($participant);
                }
                ?>
            </tbody>
        </table>
    </section>
    <div class="change_page">
        <?php change_pages($current_page, $rows_per_page, $total_number_pages); ?>
    </div>
</div>