<form action="add_db.php" method="post">
    <div class="whole_form">
        <div class="text_input">
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2">Prénom</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="prenom" required <?php if(isset($precedent_ajout['prenom'])){ echo 'value='.$precedent_ajout['prenom'];}?> />
            </div>
            <br/>
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2">Nom</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom" required <?php if(isset($precedent_ajout['nom'])){echo 'value='.$precedent_ajout['nom'];}?> />
            </div>
            <br/>
            <?php if(!isset($_GET['add_id']))
            {
            ?>
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2">Mail</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="email" <?php if(isset($precedent_ajout['email'])){echo 'value='.$precedent_ajout['email'];}?> />
            </div>
            <br/>
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2">Téléphone</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="tel" <?php if(isset($precedent_ajout['tel'])){echo 'value='.$precedent_ajout['tel'];}?> />
            </div>
            <br/>
            <?php } ?>
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2">N° de bracelet</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="bracelet" />
            </div>

            <br/>
            <div class="input-group col-md-6">
                <span class="input-group-addon" id="sizing-addon2" value=0>Price</span>
                <input type="text" class="form-control" aria-describedby="sizing-addon2" name="price" required <?php if(isset($precedent_ajout['price'])){ echo 'value='.$precedent_ajout['price'];}?> />
            </div>
        </div>
        <div class="other_input">
            <div class='col-md-3'>
                <label for="promo">Promo :</label><br />
                <select class="form-control" name="promo" id="promo">
                    <?php if(!isset($_GET['add_id'])) { ?>
                        <option> 120 </option>
                        <option> 119 </option>
                        <option> 118 </option>
                        <option> 121 </option>
                        <option> 122 </option>
                        <option> 2018 </option>
                        <option> 2019 </option>
                        <option> 2020 </option>
                        <option> 2021 </option>
                        <option> 2022 </option>
                        <option> Ingénieur </option>
                        <option> FC </option>
                        <option> Permanent </option>
                        <option> 117 </option>
                        <option> Parent </option>
                        <option selected> Artiste </option>
                        <option> MI </option>
                        <option> Invite Pumpkin </option>
                        <option> Invite 117 </option>
                        <option> Autre </option>
                    <?php }
                    if(isset($_GET['add_id'])) { ?>
                        <option value=""></option>
                        <option selected> Invite Artiste </option>
                        <option> Invite Pumpkin </option>
                        <option> Invite 117 </option>
                        <option> Invite Permanent </option>
                        <option> Autre </option>
                    <?php } ?>
                </select>

                <label for="creneau">Créneau :</label><br />
                <select required class="form-control" name="creneau" id="creneau">
                    <option <?php if(isset($_GET['add_id'])) { echo 'selected'; } ?> value="21h-21h45"> 21h-21h35 </option>
                    <option value="21h45-22h30"> 21h50-22h25 </option>
                    <option value="22h30-23h" > 22h40-23h10 </option>
                    <option> INTERDIT </option>
                    <option> 17h30 </option>
                    <option <?php if(!isset($_GET['add_id'])) { echo 'selected'; } ?> > Petite porte </option>
                    <option> Libre </option>
                    <option> BAR 117 </option>
                    <option> 18h30-19h </option>

                </select>

                <label for="paiement">Paiement :</label><br />
                <select required class="form-control" name="paiement" id="paiement">
                    <option> PayIcam </option>
                    <option <?php if(isset($_GET['add_id'])) { echo 'selected'; } ?> > Pumpkin </option>
                    <option <?php if(!isset($_GET['add_id'])) { echo 'selected'; } ?> > gratuit </option>
                    <option> cash </option>
                    <option> cb </option>
                    <option> cheque </option>
                </select>

                <label for="tickets">Tickets boissons :</label><br />
                <select required class="form-control" name="tickets" id="tickets">
                    <option> 0 </option>
                    <option> 10 </option>
                    <option> 20 </option>
                    <option> 30 </option>
                    <option> 40 </option>
                    <option> 50 </option>
                </select>

                <label for="is_icam">Icam :</label><br />
                <select required class="form-control" name="is_icam" id="is_icam">
                    <option value=1> Oui </option>
                    <option value=0 <?php if(isset($_GET['add_id'])) { echo 'selected'; } ?>> Non </option>
                </select>

            </div>

            Options Supplémentaires : <br />
            <input type="radio" name="dîner" value=1 id="dîner" /> <label for="dîner"> Dîner <br /> </label>
            <input type="radio" name="conférence" value=1 id="conférence" /> <label for="conférence"> Conférence <br /> </label>

            <?php if(isset($_GET['add_id']))
            {
                ?>
                <input name="id_icam_invitant" value="<?php echo $_GET['add_id'] ?>" />
                <?php
            }
            ?>
        </div>
    </div>

    <div class="col-md-5 submit_btn">
        <input type="submit" class="btn btn-primary" value="Enregistrer"/>
    </div>
</form>