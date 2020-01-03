<!-- Template de l'ajout de participants -->

<?php set_header_navbar("Ajout d'un participant")?>

        <h1 style="text-align: center">Ajouter un participant <?=isset($icam) ? "à " . htmlspecialchars($icam['prenom']) . ' ' . htmlspecialchars($icam['nom']) : '' . '(' . htmlspecialchars($event['name']) . ')' ?></h1><hr><br>

        <?php display_back_to_list_button($event_id); ?>

        <?php isset($icam) ? $icam['is_icam']==1 ? one_row_participant_table($icam, 'info_icam') : one_row_participant_table($icam, 'info_invite') : "" ?>

        <div class="container">
            <?php if(!isset($icam)) { ?>
            <br><div class="form-group">
                <label for="prenom">Rechercher un Icam</label>
                <input type="text" class="input-large typeahead-user form-control" name="usr" placeholder="Rechercher un utilisateur" autocomplete="off" />
            </div>
            <?php } ?>
            <form method="POST" action="php/ajout_participant.php?event_id=<?=isset($icam) ? $event_id.'&icam_id='.$icam['participant_id'] : $event_id?>">
                <h2>Renseignez les informations du participant</h2>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label for="prenom">Prénom</label>
                        <input required type="text" class="form-control" id="prenom" name="prenom">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="nom">Nom</label>
                        <input required type="text" class="form-control" id="nom" name="nom">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="price">Price</label>
                        <input required type="number" min=0 step=0.01 class="form-control" id="price" name="price">
                    </div>
                    <?php if(!isset($icam)) { ?>
                        <div class="col-sm-4 form-group">
                            <label for="email">Email</label>
                            <input required type="text" class="form-control" id="email" name="email">
                        </div>
                    <?php } ?>
                    <div class="col-sm-4 form-group">
                        <label for="bracelet_identification">Identifiant du bracelet</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="bracelet_identification" name="bracelet_identification">
                            <span id="badgeuse_indicator" class="input-group-addon" title="Connexion au lecteur de carte : non établie"><span class="glyphicon glyphicon-hdd"></span> <span class="badge badge-pill badge-warning" id="on_off">OFF</span></span>
                        </div>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="sel1">Payement:</label>
                        <select class="form-control" name="payement">
                            <option disabled> Choisissez le moyen de payement de votre participant</option>
                            <option>Espèces</option>
                            <option>Mozart</option>
                            <option>Carte bleue</option>
                            <option>Pumpkin</option>
                            <option>Lydia</option>
                            <option>Circle</option>
                            <option>Offert</option>
                            <option>à l'amiable</option>
                            <option>Autre</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <?php if(!isset($icam)) { ?>
                        <div class="col-sm-4 form-group">
                            <label for="sel1">Promo:</label>
                            <input type="hidden" name="promo" class="form-control promo">
                            <select class="form-control promo" name="promo">
                                <option disabled> Choisissez la promotion de votre participant</option>
                                <?php insert_as_select_option($promos) ?>
                            </select>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="sel1">Site:</label>
                            <input type="hidden" name="site" class="form-control site">
                            <select class="form-control site" name="site">
                                <option disabled> Choisissez le site de votre participant</option>
                                <?php insert_as_select_option($sites) ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div id="message_submit" class="container">
                    <div class="alert alert-info alert-dismissible waiting">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Parfait !</strong> Modification en cours
                    </div>
                    <br><br>
                </div>
                <div id="mandatory_options"></div>
                <div id="alerts"></div>
                <div class="text-center">
                    <button id="button_submit_form" class="btn btn-primary" type="submit">Ajouter</button>
                </div>
            </form>
        </div>
        <script src="../js/typeahead.min.js"></script>
        <script>
            var promos = '<?=json_encode($promos)?>';
            var sites = '<?=json_encode($sites)?>';
        </script>
        <?php if(isset($icam)) { ?>
            <script>
                var site_icam = '<?=$icam['site']?>';
            </script>
        <?php } ?>
        <script src="jquery/ajout_participant.js"></script>
        <script src="jquery/carte_lecteur.js"></script>
    </body>
</html>