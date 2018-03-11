<?php

function form_icam($event, $promo_specifications, $options)
{
    $firstname = 'Grégoire';
    $lastname = 'Giraud';
    $email = 'gregoire.giraud@2020.icam.fr';
    $promo = 120;
    $site = 'Lille';
    ?>
   <div id="icam_informations">
        <h4>Entrez vos informations personnelles :</h4>
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="icam_firstname">Prénom : </label>
                <input value="<?= $firstname ?>" type="text" class="form-control" name="icam_firstname" id="icam_firstname" placeholder="Prénom" disabled>
            </div>

            <div class="col-sm-4 form-group">
                <label for="icam_lastname">Nom : </label>
                <input value="<?= $lastname ?>" type="text" class="form-control" name="icam_lastname" id="icam_lastname" placeholder="Nom" disabled>
            </div>

            <div class="col-sm-4 form-group">
                <label for="icam_email">Email Icam : </label>
                <input value="<?= $email ?>" type="text" class="form-control" name="icam_email" id="icam_email" placeholder="email" disabled>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 form-group">
                <label for="icam_phone_number">Numéro de téléphone : </label>
                <input type="text" class="form-control" name="icam_phone_number" id="icam_phone_number" placeholder="Numéro de téléphone">
            </div>

            <div class="col-sm-6 form-group">
                <label for="icam_birth_date">Date de naissance :</label>
                <input type="date" class="form-control" name="icam_birth_date" id="icam_birth_date" placeholder="Date de naissance">
            </div>
        </div>
        <input type="hidden" name="price" value="<?= $promo_specifications['price'] ?>">
        <input type="hidden" name="is_icam" value=1>
        <input type="hidden" name="icam_promo_id" value=<?=$promo_specifications['promo_id']?> >
        <input type="hidden" name="icam_site_id" value=<?=$promo_specifications['site_id']?> >

    </div>
    <div id="icams_own_place">
        <label>
            Prenez vous votre propre place ? <span class="badge event_price"><?= $promo_specifications['price']. "€" ?></span>
        </label>
        <div class="form-check">
            <label class="radio-inline"><input type="radio" name="icam_takes_its_place" value=1 required>Oui</label>
            <label class="radio-inline"><input type="radio" name="icam_takes_its_place" value=0>Non</label>
        </div>
    </div>
    <div id="icam_options">
        <h4>Choisissez les options que vous voulez :</h4>
        <?php
        foreach($options as $option)
        {
            if(get_current_option_quota(array('event_id' => $event['event_id'], 'option_id' => $option['option_id'])) < $option['quota'])
            {
                $option['specifications'] = json_decode($option['specifications']);

                option_form($option, array("event_id" => $event['event_id'], "option_id" => $option['option_id'], "promo_id" => $promo_specifications['promo_id'], "site_id" => $promo_specifications['site_id']));
            }
            else
            {
                echo "Il n'y a plus de places pour l'option". $option['name']. "!";
            }
        }
        ?>
    </div>
    <?php
}

function form_guest($event, $guest_specifications, $options, $i)
{
    ?>
    <div class="guest_form col-sm-6">
        <h3 class="guest_title">
            <span class="actual_guest_title">Invité n°<?=$i?></span>
            <span class="badge event_price" style="background-color: #b94a48"><?= $guest_specifications['price']. "€" ?></span>
        </h3>
        <div class="guest_informations">
            <span class="guest_title_default_text" style="display:none">Invité n°<?=$i?></span>
            <h4>Entrez les informations de votre Invité</h4>
            <div class="row guest_inputs">
                <div class="col-sm-4 form-group">
                    <label for="guest_<?=$i?>_firstname">Prénom : </label>
                    <input type="text" class="form-control guest_firstname" name="guest_<?=$i?>_firstname" id="guest_<?=$i?>_firstname" placeholder="Prénom">
                </div>

                <div class="col-sm-5 form-group">
                    <label for="guest_<?=$i?>_lastname">Nom : </label>
                    <input type="text" class="form-control guest_lastname" name="guest_<?=$i?>_lastname" id="guest_<?=$i?>_lastname" placeholder="Nom">
                </div>

                <div class="col-sm-3 form-group">
                    <label for="guest_<?=$i?>_birthdate">Date de naissance :</label>
                    <input type="date" class="form-control guest_birthdate" name="birth_date" id="guest_<?=$i?>_birthdate" placeholder="Date de naissance">
                </div>
            </div>
            <input type="hidden" class="guest_promo_id" name="guest_promo_id" value=<?=$guest_specifications['promo_id']?> >
            <input type="hidden" class="guest_site_id" name="guest_site_id" value=<?=$guest_specifications['site_id']?> >
        </div>
        <div class="guest_options">
            <h4>Choisissez les options de votre invité :</h4>
            <?php
            foreach($options as $option)
            {
                if(get_current_option_quota(array('event_id' => $event['event_id'], 'option_id' => $option['option_id'])) < $option['quota'])
                {
                    $option['specifications'] = json_decode($option['specifications']);

                    option_form($option, array("event_id" => $event['event_id'], "option_id" => $option['option_id'], "promo_id" => $guest_specifications['promo_id'], "site_id" => $guest_specifications['site_id']));
                }
                else
                {
                    echo "Il n'y a plus de places pour l'option". $option['name']. "!";
                }
            }
            ?>
        </div>
    </div>
    <?php
}

function checkbox_form($option)
{
    ?>
    <div class="checkbox_option form-check">
        <input class="form-check-input has_option" name="has_option" type="checkbox" value=1>
        <label class="form-check-label">
            <span class="option_name"><?= $option['name'] ?></span>
            <span class="checkbox_price badge" style="background-color: #3a87ad"><?= $option['specifications']->price . ' €' ?></span>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= $option['description'] ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <input type="hidden" name="option_price" value="<?=$option['specifications']->price?>">
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}
function select_form($option)
{
    ?>
    <div class="select_option form-group">
        <label>
            <span><?= $option['name'] ?></span>
            <span class="select_price badge" style="background-color: #468847"></span>
            <button class="btn option_tooltip" type="button" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= $option['description'] ?>">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <select class="form-control">
            <option disabled <?= ($option['is_mandatory']=='0') ? "selected" : "" ?> style="display:none">Sélectionnez votre option !</option>
            <?php insert_select_options($option['specifications'], $option['is_mandatory']); ?>
        </select>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}
