<?php

/**
 * Permet d'afficher le formulaire d'inscription d'un Icam
 * @param  [array] $event                [fetch de l'event]
 * @param  [array] $promo_specifications [fetch de la promo de l'icam]
 * @param  [array] $options              [fetchAll des options de l'event]
 * @param  [array] $icam_reservation     [fetch de participant sur un edit, sinon null]
 */
function form_icam($event, $promo_specifications, $options, $icam_reservation = null)
{
    global $ticketing_state;
    $email = $_SESSION['icam_informations']->mail;
    $prenom = $_SESSION['icam_informations']->prenom;
    $nom = $_SESSION['icam_informations']->nom;

    $icam_id = $icam_reservation == null ? -1 : $icam_reservation['participant_id'];
    ?>
   <div id="icam_informations">
        <h4>
            Votre propre place
            <span class="badge bade-pill badge-success event_price"><?= $icam_reservation['price'] ?? htmlspecialchars($promo_specifications['price']) ?>€</span>
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
            $option['option_choices'] = get_option_choices($option['option_id']);
            option_form($option, $promo_specifications['promo_id'], $promo_specifications['site_id'], $icam_id);
        }
        ?>
    </div>
    <?php }
}

/**
 * Permet d'afficher le formulaire d'inscription d'un invité
 * @param  [array] $event                [fetch de l'event]
 * @param  [array] $guest_specifications [fetch de la promo des invités]
 * @param  [array] $options              [fetchAll des options de l'event]
 * @param  [int] $i              [simple compteur, pour pas faire de problèmes et mettre des ids différentes]
 * @param  [array] $guest_reservation     [fetch de participant sur un edit, sinon null]
 */
function form_guest($event, $guest_specifications, $options, $i, $guest_reservation=null)
{
    global $ticketing_state;
    $guest_id = $guest_reservation == null ? -1 : $guest_reservation['participant_id'];
    ?>
    <div class="guest_form col-sm-6 <?= $guest_reservation!=null ? "previous_guest" : "" ?>">
        <h3 class="guest_title">
            <span class="actual_guest_title">Invité n°<?=$i?></span>
            <span class="badge badge-pill <?=$guest_reservation==null ? 'badge-error' : 'badge-success' ?> event_price"><?= isset($guest_reservation['price']) ? htmlspecialchars($guest_reservation['price']) : htmlspecialchars($guest_specifications['price']) ?>€ </span>
        </h3>
        <div class="guest_informations">
            <span class="guest_title_default_text" style="display:none">Invité n°<?=$i?></span>
            <h4>Entrez les informations de votre Invité</h4>
            <div class="row guest_inputs">
                <div class="col-sm-4 form-group">
                    <label for="guest_<?=$i?>_firstname">Prénom : </label>
                    <input <?=$ticketing_state !='open' ? "disabled" : "" ?> value="<?= isset($guest_reservation['prenom']) ? htmlspecialchars($guest_reservation['prenom']) : '' ?>" type="text" class="form-control guest_firstname" name="guest_<?=$i?>_firstname" id="guest_<?=$i?>_firstname" placeholder="Prénom">
                </div>

                <div class="col-sm-5 form-group">
                    <label for="guest_<?=$i?>_lastname">Nom : </label>
                    <input <?=$ticketing_state !='open' ? "disabled" : "" ?> value="<?= isset($guest_reservation['nom']) ? htmlspecialchars($guest_reservation['nom']) : '' ?>" type="text" class="form-control guest_lastname" name="guest_<?=$i?>_lastname" id="guest_<?=$i?>_lastname" placeholder="Nom">
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
                $option['option_choices'] = get_option_choices($option['option_id']);
                option_form($option, $guest_specifications['promo_id'], $guest_specifications['site_id'], $guest_id);
            }
            ?>
        </div>
        <?php } ?>
    </div>
    <?php
}

/**
 * Permet d'afficher une checkbox option
 * @param  [array]  $option     [fetch de options + ajout d'un fetch du bon option_choices à l'index $option['option_choices'][0]]
 * @param  mixed $price_paid [si sur un edit, price_paid indiqué, pour afficher combien le participant a réellement payé (possible de changer le prix du checkbox, alors celui de l'option serait faux)]
 */
function checkbox_form($option, $price_paid=false)
{
    ?>
    <div class="checkbox_option form-check" <?= $price_paid ? "data-payed=1" : "" ?> >
        <input class="form-check-input has_option" name="has_option" type="checkbox" value="<?=$option['option_choices'][0]['choice_id']?>" <?= $price_paid ? "checked disabled data-payed=1" : "" ?> >
        <label class="form-check-label">
            <span class="option_name"><?= htmlspecialchars($option['name']) ?></span>
            <span class="checkbox_price badge badge-pill badge-info"><?= $price_paid!==false ? htmlspecialchars($price_paid) : htmlspecialchars($option['option_choices'][0]['price'])?>€</span>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <input type="hidden" name="option_price" value="<?=htmlspecialchars($option['option_choices'][0]['price'])?>">
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
        <input type="hidden" class="option_article_id" name="option_article_id" value="<?=$option['option_choices'][0]['scoobydoo_article_id']?>">
    </div>
    <?php
}

/**
 * Permet d'afficher une select option
 * @param  [array]  $option     [fetch de options + ajout d'un fetchAll des option_choices à l'index $option['option_choices']]
 * @param  [mixed] $select_choice [fetch de option_choices]
 */
function select_form($option, $select_choice=null)
{
    global $ticketing_state;
    ?>
    <div class="select_option form-group" <?= $select_choice!=null ? "data-payed=1" : "" ?>>
        <label>
            <span><?= htmlspecialchars($option['name']) ?></span>
            <span class="select_price badge badge-pill <?= isset($select_choice) ? 'badge-success' : 'badge-info' ?>"></span>
            <button class="btn option_tooltip" type="button" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <select class="form-control">
            <option disabled <?= ($option['is_mandatory']=='0') ? "selected" : "" ?> style="display:none">Sélectionnez votre option !</option>
            <?php insert_according_select_options($option, $select_choice); ?>
        </select>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}

/**
 * Insérer les choix possibles, s'ils ne sont pas payés, sinon, afficher juste celui déjà pris
 * @param  [array]  $option     [fetch de options + ajout d'un fetchAll des option_choices à l'index $option['option_choices']]
 * @param  [mixed] $select_choice [fetch de option_choices]
 */
function insert_according_select_options($option, $select_choice=null)
{
    $compteur=0;
    if($select_choice==null)
    {
        foreach($option['option_choices'] as $option_choice)
        {
            $suboption_quota = $option_choice['quota']==null ? INF : $option_choice['quota'];
            if(get_current_select_option_quota(array("event_id" => $option['event_id'], "choice_id" => $option_choice['choice_id'])) < $suboption_quota)
            {
                ?>
                <option value="<?= $option_choice['choice_id']?>" <?=($option['is_mandatory']==1 and $compteur==0) ? 'selected' : ''?> >
                    <?= htmlspecialchars($option_choice['name']) . ' (' . htmlspecialchars($option_choice['price']) . '€)' ?>
                </option>
                <?php
                $compteur++;
            }
        }
    }
    else
    {
        ?>
        <option value="<?= $select_choice['choice_id']?>" selected data-payed=1><?= htmlspecialchars($select_choice['name']) . '(' . $select_choice['price_paid'] . '€)' ?></option>
        <?php
    }
}

/**
 * Permet d'afficher le message le message d'erreur indiquant qu'une transaction est en attente
 * @param  [string] $payicam_transaction_url [url à laquelle payer la transaction]
 * @param  [int] $event_id                [sert à mettre le bon lien s'il annule sa transaction]
 */
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

/**
 * On affiche les évènements, avec leur titre & description, ainsi que le lien vers la billetterie. Selon le ticketing_state, l'affichage sera légèrement différent
 * @param  string $ticketing_state
 * @param  array $event                [fetch de events]
 * @param  boolean $icam_has_reservation [true si l'Icam a une réservation]
 */
function display_event($ticketing_state, $event, $icam_has_reservation)
{
    global $_CONFIG;
    switch ($ticketing_state)
    {
        case 'open':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-success">En cours</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <a href="<?=$_CONFIG['public_url']?>inscriptions/<?=$icam_has_reservation ? "edit_reservation.php" : "inscriptions.php" ?>?event_id=<?=$event['event_id']?>" class="btn btn-primary col-sm-3">
                        <?=$icam_has_reservation ? "Modifier sa réservation" : "S'inscrire" ?>
                    </a>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
        case 'coming soon':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-info">Ouvre bientôt</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <button class="btn btn-primary col-sm-3" disabled>Inscrivez vous</button><!-- On ne met pas le lien ici, ça n'a aucun intérêt -->
                </div>
            </div>
            <hr>
            <?php
            break;
        }
        case 'ended not long ago and reservation':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-info">Terminé</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <a href="<?=$_CONFIG['public_url']?>inscriptions/edit_reservation.php?event_id=<?=$event['event_id']?>" class="btn btn-primary col-sm-3">Regarder sa réservation</a>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
    }
}