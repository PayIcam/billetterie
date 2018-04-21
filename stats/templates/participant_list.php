<?php set_header_navbar('Liste des participants : ' . $event['name'])?>
        <div class="container all_index">

            <div class="row">
                <div class="col-md-5">
                    <p><h3>Liste des participants pour l'évènement : <?= htmlspecialchars($event['name'])?></h3></p>
                    <p>Actuellement <?= $number_participants ?> invités</p>
                    <p><?=$_SESSION['search_match'] ?? '' ?></p>
                </div>
            </div>
            <form action="participants.php?event_id=<?=$_GET['event_id']?>&rows=<?=$rows_per_page?>" method="post">
                <div class="row">
                    <div class= "col-md-3">
                        <input type="input-medium search-query" class="form-control" name ="recherche" id="recherche" placeholder="Nom, prénom, promo..." value="<?= isset($_POST['recherche']) ? htmlspecialchars($_POST['recherche']) : '' ?>">
                    </div>
                    <div class="col-md-9">
                        <button class="inline btn btn-primary" type="submit">Rechercher</button>
                        <a type="button" class="btn btn-primary" href="statistiques.php?event_id=<?=$event_id?>" class="btn btn-primary">Statistiques de l'évènement</a>
                        <?php if($Auth->hasRole('admin')) { ?> <a type="button" class="btn btn-success" href="ajout_participant.php?event_id=<?=$event_id?>" class="btn btn-primary">Ajouter un invité</a> <?php } ?>
                    </div>
                    <?php echo '<br><br>'; display_go_to_arrivals($event_id); ?>
                </div>
            </form>
            <br>
            <div>
                <h1 class="numero_page"> Page <?= $current_page . "/" . $total_number_pages ?> </h1>
            </div>

            <?php isset($_SESSION['search_match']) ? htmlspecialchars($_SESSION['search_match']) : "";
            unset($_SESSION['search_match']);
            change_number_rows($rows_per_page);
            ?>
            <div class="change_page">
                <?php change_pages($current_page, $rows_per_page, $total_number_pages) ?>
            </div>
            <section class="row" id="tableau">
                <table class="participant_infos table table-striped">
                    <thead>
                        <?php display_liste_head(); ?>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($participants as $participant)
                        {
                            $participant = prepare_participant_displaying($participant);
                            display_participant_info($participant);
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <div class="change_page">
                <?php change_pages($current_page, $rows_per_page, $total_number_pages) ?>
            </div>
        </div>
        <script src="jquery/participants.js"></script>
    </body>
</html>