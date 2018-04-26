<?php set_header_navbar("Ajout d'un participant")?>

        <h1 style="text-align: center">Ajouter un participant <?=isset($icam) ? "à " . htmlspecialchars($icam['prenom']) . ' ' . htmlspecialchars($icam['nom']) : '' . '(' . htmlspecialchars($event['name']) . ')' ?></h1><hr><br>

        <?php display_back_to_list_button($event_id); ?>

        <?php isset($icam) ? $icam['is_icam']==1 ? one_row_participant_table($icam, 'info_icam') : one_row_participant_table($icam, 'info_invite') : "" ?>

        <form method="POST" action="php/ajout_participant.php?event_id=<?=isset($icam) ? $event_id.'&icam_id='.$icam['participant_id'] : $event_id?>">
            <div class="container">
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
                            <label for="telephone">Téléphone</label>
                            <input required type="text" class="form-control" id="telephone" name="telephone">
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="email">Email</label>
                            <input required type="text" class="form-control" id="email" name="email">
                        </div>
                    <?php } ?>
                    <div class="col-sm-4 form-group">
                        <label for="bracelet_identification">Identifiant du bracelet</label>
                        <input type="text" class="form-control" id="bracelet_identification" name="bracelet_identification">
                    </div>
                </div>
                <div class="row">
                    <?php if(!isset($icam)) { ?>
                        <div class="col-sm-4 form-group">
                            <label for="sel1">Promo:</label>
                            <select class="form-control" name="promo">
                                <option disabled> Choisissez la promotion de votre participant</option>
                                <?php insert_as_select_option($promos) ?>
                            </select>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="sel1">Site:</label>
                            <select class="form-control" name="site">
                                <option disabled> Choisissez le site de votre participant</option>
                                <?php insert_as_select_option($sites) ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="col-sm-4 form-group">
                        <label for="sel1">Payement:</label>
                        <select class="form-control" name="payement">
                            <option disabled> Choisissez le moyen de payement de votre participant</option>
                            <option>Espèces</option>
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
                <div id="message_submit" class="container">
                    <div class="alert alert-info alert-dismissible waiting">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Parfait !</strong> Modification en cours
                    </div>
                    <br><br>
                </div>
                <div id="alerts"></div>
                <div class="text-center">
                    <button id="button_submit_form" class="btn btn-primary" type="submit">Ajouter</button>
                </div>
            </div>
        </form>
        <script src="jquery/ajout_participant.js"></script>
    </body>
</html>