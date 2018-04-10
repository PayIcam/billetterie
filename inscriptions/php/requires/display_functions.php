<?php

function form_icam($event, $promo_specifications, $options, $icam_reservation = null)
{
    $email = $_SESSION['icam_informations']->mail;
    $prenom = $_SESSION['icam_informations']->prenom;
    $nom = $_SESSION['icam_informations']->nom;

    $icam_id = $icam_reservation == null ? -1 : $icam_reservation['participant_id'];
    ?>
   <div id="icam_informations">
        <h4>
            Votre propre place
            <span class="badge event_price" style="background-color: #468847"><?= htmlspecialchars($promo_specifications['price']). "€" ?></span>
        </h4>

        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="icam_firstname">Prénom : </label>
                <input value="<?= $icam_reservation['prenom'] ?? $prenom ?>" type="text" class="form-control" name="icam_firstname" id="icam_firstname" placeholder="Prénom" disabled>
            </div>

            <div class="col-sm-4 form-group">
                <label for="icam_lastname">Nom : </label>
                <input value="<?= $icam_reservation['nom'] ?? $nom ?>" type="text" class="form-control" name="icam_lastname" id="icam_lastname" placeholder="Nom" disabled>
            </div>

            <div class="col-sm-4 form-group">
                <label for="icam_email">Email Icam : </label>
                <input value="<?= $icam_reservation['email'] ?? $email ?>" type="text" class="form-control" name="icam_email" id="icam_email" placeholder="email" disabled>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 form-group">
                <label for="icam_phone_number">Numéro de téléphone : </label>
                <input value="<?= isset($icam_reservation['telephone']) ? htmlspecialchars($icam_reservation['telephone']) : '' ?>" type="text" class="form-control" name="icam_phone_number" id="icam_phone_number" placeholder="Numéro de téléphone">
            </div>
            <?php if(isset($icam_reservation['bracelet_identification'])) { ?>
            <div class="col-sm-6 form-group">
                <label for="icam_phone_number">Identifiant de bracelet : </label>
                <input disabled value="<?= $icam_reservation['bracelet_identification']?>" type="text" class="form-control">
            </div>
            <?php } ?>
        </div>
        <input type="hidden" name="price" value="<?= htmlspecialchars($promo_specifications['price']) ?>">
        <input type="hidden" name="is_icam" value=1>
        <input type="hidden" name="icam_promo_id" value="<?=$promo_specifications['promo_id']?>" >
        <input type="hidden" name="icam_site_id" value="<?=$promo_specifications['site_id']?>" >
        <input type="hidden" name="icams_event_article_id" value="<?=$promo_specifications['scoobydoo_article_id']?>">
        <?php if($icam_reservation != null)
        {
            ?> <input type="hidden" name="icam_id" value="<?=$icam_id?>" > <?php
        }?>

    </div>

    <?php if(count($options)>0) { ?>
    <div id="icam_options">
        <h4>Choisissez les options que vous voulez :</h4>
        <?php
        foreach($options as $option)
        {
            $option['specifications'] = json_decode($option['specifications']);
            option_form($option, $promo_specifications['promo_id'], $promo_specifications['site_id'], $icam_id);
        }
        ?>
    </div>
    <?php }
}

function form_guest($event, $guest_specifications, $options, $i, $guest_reservation=null)
{
    $guest_id = $guest_reservation == null ? -1 : $guest_reservation['participant_id'];
    ?>
    <div class="guest_form col-sm-6 <?= $guest_reservation!=null ? "previous_guest" : "" ?>">
        <h3 class="guest_title">
            <span class="actual_guest_title">Invité n°<?=$i?></span>
            <span class="badge event_price" style="background-color: #b94a48"><?= htmlspecialchars($guest_specifications['price']). "€" ?></span>
        </h3>
        <div class="guest_informations">
            <span class="guest_title_default_text" style="display:none">Invité n°<?=$i?></span>
            <h4>Entrez les informations de votre Invité</h4>
            <div class="row guest_inputs">
                <div class="col-sm-4 form-group">
                    <label for="guest_<?=$i?>_firstname">Prénom : </label>
                    <input value="<?= isset($guest_reservation['prenom']) ? htmlspecialchars($guest_reservation['prenom']) : '' ?>" type="text" class="form-control guest_firstname" name="guest_<?=$i?>_firstname" id="guest_<?=$i?>_firstname" placeholder="Prénom">
                </div>

                <div class="col-sm-5 form-group">
                    <label for="guest_<?=$i?>_lastname">Nom : </label>
                    <input value="<?= isset($guest_reservation['nom']) ? htmlspecialchars($guest_reservation['nom']) : '' ?>" type="text" class="form-control guest_lastname" name="guest_<?=$i?>_lastname" id="guest_<?=$i?>_lastname" placeholder="Nom">
                </div>
            </div>
            <input type="hidden" class="guest_promo_id" name="guest_promo_id" value=<?=$guest_specifications['promo_id']?> >
            <input type="hidden" class="guest_site_id" name="guest_site_id" value=<?=$guest_specifications['site_id']?> >
            <?php if($guest_reservation != null)
            {
                ?> <input type="hidden" name="guest_id" value="<?=$guest_id?>" > <?php
            }?>
        </div>
        <?php if(count($options)>0) { ?>
        <div class="guest_options">
            <h4>Choisissez les options de votre invité :</h4>
            <?php
            foreach($options as $option)
            {
                $option['specifications'] = json_decode($option['specifications']);
                option_form($option, $guest_specifications['promo_id'], $guest_specifications['site_id'], $guest_id);
            }
            ?>
        </div>
        <?php } ?>
    </div>
    <?php
}

function checkbox_form($option, $checked=false)
{
    ?>
    <div class="checkbox_option form-check" <?= $checked ? "data-payed=1" : "" ?> >
        <input class="form-check-input has_option" name="has_option" type="checkbox" value="<?=$option['specifications']->scoobydoo_article_id?>" <?= $checked ? "checked disabled data-payed=1" : "" ?> >
        <label class="form-check-label">
            <span class="option_name"><?= htmlspecialchars($option['name']) ?></span>
            <span class="checkbox_price badge" style="background-color: #3a87ad"><?= htmlspecialchars($option['specifications']->price) . ' €' ?></span>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <input type="hidden" name="option_price" value="<?=htmlspecialchars($option['specifications']->price)?>">
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
        <input type="hidden" class="option_article_id" name="option_article_id" value="<?=$option['specifications']->scoobydoo_article_id?>">
    </div>
    <?php
}
function select_form($option, $option_subname=null)
{
    ?>
    <div class="select_option form-group" <?= $option_subname!=null ? "data-payed=1" : "" ?>>
        <label>
            <span><?= $option['name'] ?></span>
            <span class="select_price badge" style="background-color: #468847"></span>
            <button class="btn option_tooltip" type="button" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <select class="form-control">
            <option disabled <?= ($option['is_mandatory']=='0') ? "selected" : "" ?> style="display:none">Sélectionnez votre option !</option>
            <?php insert_according_select_options($option, $option_subname); ?>
        </select>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}

function insert_according_select_options($option, $option_subname=null)
{
    $compteur=0;
    foreach($option['specifications'] as $option_specification)
    {
        if($option_subname==null)
        {
            $suboption_quota = $option_specification->quota==null ? INF : $option_specification->quota;
            if(get_current_select_option_quota(array("event_id" => $option['event_id'], "option_id" => $option['option_id'], "subname" => $option_specification->name)) < $suboption_quota)
            {
                ?>
                <option value="<?= $option_specification->scoobydoo_article_id?>" <?=($option['is_mandatory']==1 and $compteur==0) ? 'selected' : ''?> >
                    <?= htmlspecialchars($option_specification->name) . ' (' . htmlspecialchars($option_specification->price) . '€)' ?>
                </option>
                <?php
                $compteur++;
            }
        }
        elseif(trim($option_subname) == trim($option_specification->name))
        {
            ?>
            <option value="<?= $option_specification->scoobydoo_article_id?>" selected data-payed=1><?= htmlspecialchars($option_specification->name) . '(' . $option_specification->price . '€)' ?></option>
            <?php
        }
    }
}
function cancel_or_finish_transaction($payicam_transaction_url, $event_id)
{
    global $_CONFIG;
    ?>
    <p class="alert alert-warning">
        Vous avez déjà soumis une réservation, mais vous ne l'avez pas encore réglée.<br>
        Vous pouvez la régler ou bien l'annuler.<br>
        Dépéchez vous, avant qu'elle ne soit plus valide.<br>
        <br>
        <a href="<?= $payicam_transaction_url ?>" class="btn btn-primary">Régler la réservation</a> - <a href="<?= $_CONFIG['public_url'].'inscriptions/php/cancel_transaction.php?event_id='.$event_id ?>" class="btn btn-danger">Annuler la réservation</a>
    </p>

    <?php
}