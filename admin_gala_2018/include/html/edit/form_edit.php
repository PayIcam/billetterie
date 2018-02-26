<div class="container">
    <form action ="edit_db.php" method="post">
        <input type="hidden" value="<?php echo htmlspecialchars($edit_id); ?>" name="edit_id" />
        <input type="hidden" value="<?php echo htmlspecialchars($participant_sous_edit['is_icam']); ?>" name="is_icam" />
        <input type="hidden" value="<?php echo htmlspecialchars($participant_sous_edit['nombre_invites_et_total']); ?>" name="message_invite" />

        <?php if($participant_sous_edit['is_icam'] ==0)
        { ?>
            <div class="input-group col-md-6" id="nom">
                <span class="input-group-addon" id="sizing-addon2">Nom</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom"
                <?php
                if (isset($participant_sous_edit['nom'])) { echo htmlspecialchars('value='. $participant_sous_edit['nom'].''); } ?>
                >
            </div>
            <br>
            <div class="input-group col-md-6" id='prenom'>
                <span class="input-group-addon" id="sizing-addon2">Prénom</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="prenom"
                <?php
                if (isset($participant_sous_edit['prenom'])) { echo htmlspecialchars('value='. $participant_sous_edit['prenom'].''); } ?>
                >
            </div>
        <?php } ?>
        <!--
        <?php if($participant_sous_edit['is_icam'] ==1)
        { ?>
        <div class="input-group col-md-6" id='telephone'>
            <span class="input-group-addon" id="sizing-addon2"><i class="fas fa-phone"></i></span>
            <input type="text" class="form-control" placeholder="Telephone" aria-describedby="sizing-addon2" name="telephone"
            <?php
            if (isset($participant_sous_edit['telephone'])) { echo htmlspecialchars('value='. $participant_sous_edit['telephone'].''); } ?>
            >
        </div>
        <?php } ?> -->
        <br>
        <div class="champ_bracelet_general">
            <div class="champ_bracelet input-group col-md-6" id='bracelet'>
                <span class="input-group-addon" id="sizing-addon2">N° de bracelet</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="bracelet_id"
                <?php
                if (isset($participant_sous_edit['bracelet_id'])) { echo htmlspecialchars('value='. $participant_sous_edit['bracelet_id'] .''); } ?>
                >
            </div>
            <div>
                <span class="erreur_saisie_bracelet"> Le numéro de bracelet est incorrect. Ressaisissez le.</span>
            </div>
        </div>
        <br>
        <div class='col-md-3'>
            <label for="creneau">Créneaux d'entrée :</label><br />
            <select class="form-control" name="creneau" id="creneau">
                <?php
                $selected=0;
                foreach($current_creneaux_quotas as $current_creneau_quota)
                {
                    if($participant_sous_edit['plage_horaire_entrees']==$current_creneau_quota['creneau'])
                    {
                        $selected=1;
                        echo '<option selected value="'.$current_creneau_quota['creneau'].'">'.$current_creneau_quota['vrai_creneau'].'</option>';
                    }
                    elseif($current_creneau_quota['actuellement'] < $current_creneau_quota['quota'])
                    {
                        echo '<option value="'.$current_creneau_quota['creneau'].'">'.$current_creneau_quota['vrai_creneau'].'</option>';
                    }
                }
                if($selected==0)
                {
                    echo '<option selected>'.$participant_sous_edit['plage_horaire_entrees'].'</option>';
                }
                ?>
            </select>
        </div>
        <br>
        <div class="col-md-5">
           <input type="submit" class="btn btn-primary" value="Enregistrer"/>
        </div>
    </form>
</div>