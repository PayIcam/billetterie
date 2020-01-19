<?php

/**
 * Affiche juste le select permettant de choisir le nombre de lignes à avoir par page
 * @param  [int] $rows_per_page [nombre de lignes actuel, afin de sélectionner le chjoix correspondant]
 */
function change_number_rows($rows_per_page)
{
    global $event_id;
    ?>
    <div id="change_number_rows">
        <form method="get" action="participants.php?event_id=<?=$event_id?>">
            <input type="hidden" value="<?=$event_id?>" name="event_id">
            <label for ="#change_rows"> Nombre de lignes par page : </label> <br/>
            <select class="custom-select mr-sm-2" id="change_rows" name="rows">
                <option <?= $rows_per_page==10 ? 'selected' : "" ?> >10</option>
                <option <?= $rows_per_page==15 ? 'selected' : "" ?> >15</option>
                <option <?= $rows_per_page==20 ? 'selected' : "" ?> >20</option>
                <option <?= $rows_per_page==25 ? 'selected' : "" ?> >25</option>
                <option <?= $rows_per_page==50 ? 'selected' : "" ?> >50</option>
                <option <?= $rows_per_page==100 ? 'selected' : "" ?> >100</option>
                <option <?= $rows_per_page==250 ? 'selected' : "" ?> >250</option>
            </select>
        </form>
    </div>
    <?php
}

/**
 * On utilise cette foncion pour afficher l'affichage des boutons de changements de page
 * La recherche est transmise en POST, ceci fait que les retours en arrière sont légèrement moches
 * Pour que ça ne soit pas le cas s'il n'y a pas de recherche, on met un lien, sinon on met la recherche en POST, et on redirige vers la bonne page
 * @param  [string] $link [lien vers lequel rediriger, avec le numéro de la page & le nombre de lignes si rempli]
 * @param  [string] $sign [Texte du bouton]
 */
function create_link_or_form($link, $sign)
{
    ?>
    <span class="change_page_text">
        <?php
        if(isset($_POST['recherche']))
        {
            ?>
            <form method="post" action="<?= $link ?>">
                <input type="hidden" name="recherche" value="<?=$_POST['recherche']?>" >
                <button type="submit" class="btn btn-primary"><?=$sign?></button>
            </form>
            <?php
        }
        else
        {
            ?>
            <a href="<?=$link?>"><button class="btn btn-primary"><?=$sign?></button></a>
            <?php
        }
        ?>
    </span>
    <?php
}

/**
 * Fonction qui permet d'afficher les boutons de changements de page, et seulement ceux nécessaires.
 * On va d'abord regarer s'il faut chaque bouton, et si c'est le cas, on l'affiche, grâce à la bonne des 4 fonctions, qui appelerons avec les bons paramètres create_link_or_form définie juste au dessus
 * @param  [int] $current_page
 * @param  [int] $rows_per_page
 * @param  [int] $total_number_pages
 */
function change_pages($current_page, $rows_per_page, $total_number_pages)
{
    if(!function_exists('next_page')) {
    function next_page($current_page, $rows_per_page)
    {
        $wanted_page = $current_page+1;
        $page_text='&page=' . $wanted_page;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
        create_link_or_form($link, ">");
    }}
    if(!function_exists('prev_page')) {
    function prev_page($current_page, $rows_per_page)
    {
        $wanted_page = $current_page-1;
        $page_text='&page=' . $wanted_page;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
        create_link_or_form($link, "<");
    }}
    if(!function_exists('last_page')) {
    function last_page($current_page, $rows_per_page, $total_number_pages)
    {
        $page_text='&page=' . $total_number_pages;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
        create_link_or_form($link, ">>>");
    }}
    if(!function_exists('first_page')) {
    function first_page($current_page, $rows_per_page)
    {
        $page_text='&page=' . 1;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
        create_link_or_form($link, "<<<");
    }}
    if($current_page>2)
    {
        first_page($current_page, $rows_per_page);
    }
    if($current_page>1)
    {
        prev_page($current_page, $rows_per_page);
    }
    if($total_number_pages >1 and $current_page <$total_number_pages)
    {
        next_page($current_page, $rows_per_page);
    }
    if($current_page<$total_number_pages-1)
    {
        last_page($current_page, $rows_per_page, $total_number_pages);
    }
}

/**
 *
 * Cette fonction va créer la structure du tableau.
 *
 * Elle marche en ne spécifiant aucun argument, alors tout sera affiché
 * Sinon, spécifier des arguements permet d'enlever un champ particulier, ou de l'ajouter
 *
 * En particulier, certains arguments permettent de ne pas tout retapper, tout étant géré dans la fonction
 *
 * @param  string  $specification     [default : "", sinon peux prendre les valeurs dans ['info_icam', 'info_invite', 'link_icam', 'link_invite'] ]
 * @param  boolean $id                [default : true]
 * @param  boolean $status            [default : false]
 * @param  boolean $personnal_infos   [default : true]
 * @param  boolean $options           [default : true]
 * @param  boolean $edit              [default : true]
 * @param  boolean $additions         [default : true]
 * @param  boolean $bracelet          [default : true]
 * @param  boolean $date_payement     [default : false]
 * @param  boolean $pending_indicator [default : true]
 * @param  boolean $guest_info        [default : true]
 * @param  boolean $arrivals        [default : true]
 */
function display_liste_head($specification="", $id=true, $status=false, $personnal_infos=true, $options=true, $edit=true, $additions=true, $bracelet=true, $date_payement=false, $pending_indicator=true, $guest_info=true, $arrivals=true)
{
    global $Auth, $admin_rights;
    if(!$Auth->hasRole('super-admin'))
    {
        $id = false;
    }
    if(!$admin_rights)
    {
        $additions = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $additions = false;
        $arrivals = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $guest_number = false;
        $pending_indicator = false;
        $edit = false;
        $additions = false;
        $arrivals = false;
    }
    elseif($specification == 'link_icam')
    {
        $additions = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $guest_number = false;
        $additions = false;
        $pending_indicator = false;
    }
    ?>
        <?php if($id) { ?> <th scope="col">ID</th> <?php } ?>
        <?php if($status) { ?> <th scope="col">Status</th> <?php } ?>
        <th scope="col">Prénom</th>
        <th scope="col">Nom</th>
        <th scope="col">Promo</th>
        <?php if($bracelet) { ?> <th scope="col">Bracelet</th> <?php } ?>
        <!-- <th scope="col">Créneau</th> -->
        <!-- <th scope="col">Tickets Boissons</th> -->
        <?php if($personnal_infos) { ?> <th scope="col">Informations</th> <?php } ?>
        <?php if($options) { ?> <th scope="col">Options</th> <?php } ?>
        <?php if($guest_info) { ?> <th scope="col">Invités</th> <?php } ?>
        <?php if($pending_indicator) { ?> <th scope="col">Attente</th> <?php } ?>
        <?php if($edit) { ?> <th scope="col">Editer</th> <?php } ?>
        <?php if($additions) { ?> <th scope="col">Ajouts</th> <?php } ?>
        <?php if($arrivals) { ?> <th scope="col">Entrer/sortir</th> <?php } ?>
    <?php
}

/**
 * Affiche le bouton permettant d'éditer un participant
 * @param  [array] $participant
 */
function link_to_edit_reservation($participant)
{
    $event_id = $_GET['event_id'];
    ?>
    <td>
        <a class="btn btn-primary" href="edit_participant.php?event_id=<?=$event_id?>&participant_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-edit"></span>
        </a>
    </td>
    <?php
}
/**
 * Affiche les 2 boutons d'ajout d'options, ou d'invités si voulu.
 * On affiche le bouton d'ajout d'options si le participant ne les a pas déjà toutes
 * On affiche le bouton d'ajout d'invités, si c'est un Icam.
 * @param  [array] $participant
 */
function links_to_various_addition($participant)
{
    $event_id = $_GET['event_id'];
    ?>
    <td>
        <?php if($participant['is_icam']==1) { ?>
        <a class="btn btn-primary" href="ajout_participant.php?event_id=<?=$event_id?>&icam_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-plus"></span>
        </a>
        <?php }
        if(!empty(get_optional_options(array('event_id' => $participant['event_id'], 'promo_id' => $participant['promo_id'], 'site_id' => $participant['site_id'], 'participant_id' => $participant['participant_id'])))) { ?>
        <a class="btn btn-primary" href="ajout_options.php?event_id=<?=$event_id?>&participant_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-gift"></span>
        </a>
        <?php } ?>
    </td>
    <?php
}

/**
 * Affiche seulement la promo, mais avec une classe particulière, pour y voir plus clair (jaune invités / vert graduated icam / bleu icam student)
 * @param  [array] $promo [fetch de promos]
 */
function display_promo($promo)
{
    $promo_still_student = get_promo_status($promo);
    $class = $promo == 'Invités' ? 'warning' : ($promo_still_student==1 ? 'success' : 'info');
    ?>
    <td class="<?=$class?>"> <?=$promo?> </td>
    <?php
}

/**
 *
 * Cette fonction va créer le corps du tableau
 *
 * Elle marche en ne spécifiant aucun argument, alors tout sera affiché
 * Sinon, spécifier des arguements permet d'enlever un champ particulier, ou de l'ajouter
 *
 * En particulier, certains arguments permettent de ne pas tout retapper, tout étant géré dans la fonction
 *
 * @param  string  $specification     [default : "", sinon peux prendre les valeurs dans ['info_icam', 'info_invite', 'link_icam', 'link_invite'] ]
 * @param  boolean $id                [default : true]
 * @param  boolean $status            [default : false]
 * @param  boolean $personnal_infos   [default : true]
 * @param  boolean $options           [default : true]
 * @param  boolean $edit              [default : true]
 * @param  boolean $additions         [default : true]
 * @param  boolean $bracelet          [default : true]
 * @param  boolean $date_payement     [default : false]
 * @param  boolean $pending_indicator [default : true]
 * @param  boolean $guest_info        [default : true]
 * @param  boolean $arrivals        [default : true]
 */
function display_participant_info($participant, $specification="", $id=true, $status=false, $options=true, $edit=true, $additions=true, $bracelet=true, $personnal_infos=true, $date_payement=false, $pending_indicator=true, $guest_info=true, $arrivals=true)
{
    global $Auth, $admin_rights;
    if(!$Auth->hasRole('super-admin'))
    {
        $id = false;
    }
    if(!$admin_rights)
    {
        $additions = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $additions = false;
        $arrivals = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $pending_indicator = false;
        $edit = false;
        $additions = false;
        $arrivals = false;
    }
    elseif($specification == 'link_icam')
    {
        $additions = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $additions = false;
        $pending_indicator = false;
    }
    ?>
    <tr data-participant_id=<?=$participant['participant_id']?> >
        <?= $id ? "<td>" . $participant['participant_id'] . "</td>" : "" ?>
        <?= $status ? "<td>" . $participant['status'] . "</td>" : "" ?>
        <td class="prenom"><?= htmlspecialchars($participant['prenom']) ?></td>
        <td class="nom"><?= htmlspecialchars($participant['nom']) ?></td>
        <?= display_promo($participant['promo']); ?>
        <?= $bracelet ? "<td class='bracelet_identification'><span class='badge badge-pill badge-info'>" . $participant['bracelet_identification'] . "</span></td>" : "" ?>
        <?= $personnal_infos ? display_personnal_informations($participant) : "" ?>
        <?= $options ? display_options($participant) : "" ?>
        <?= $guest_info ? display_guest_infos($participant) : "" ?>
        <?= $pending_indicator ? display_pending_reservations($participant) : "" ?>
        <?= $edit ? link_to_edit_reservation($participant) : "" ?>
        <?= $additions ? links_to_various_addition($participant) : "" ?>
        <?= $arrivals ? display_validate_button($participant) : "" ?>
    </tr>
    <?php
}

/**
 * Cette fonction permet d'afficher une seule ligne sur les infos d'un participant. Typiquement, elle est appelée dans les pages style ajout_options, ajout_invites à un Icam & edit_participant
 * @param  [array] $participant   [ce qui sort de prepare_participant_displaying]
 * @param  string $specification [default : "", sinon peux prendre les valeurs dans ['info_icam', 'info_invite', 'link_icam', 'link_invite'] ]
 */
function one_row_participant_table($participant, $specification="")
{
    ?>
    <div class="container">
        <section class="row" id="tableau">
            <table class="participant_infos table table-striped">
                <thead>
                    <?php display_liste_head($specification) ?>
                </thead>
                <tbody>
                    <?php display_participant_info($participant, $specification) ?>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}

/**
 * Affiche un checkbox sans vérifier quoi que ce soit
 * @param  [array] $option [fetch de options avec un fetch du checkbox dans $option['option_choices'] ]
 */
function checkbox_form_basic($option)
{
    ?>
    <div class="checkbox_option form-check">
        <input class="form-check-input has_option" name="has_option" type="checkbox" value="<?=$option['option_choices']['choice_id']?>" >
        <label class="form-check-label">
            <span class="option_name"><?= htmlspecialchars($option['name']) ?></span>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <input type="hidden" name="choice_id" value="<?=$option['option_choices']['choice_id']?>">
        <input type="hidden" class="option_article_id" name="option_article_id" value="<?=$option['option_choices']['scoobydoo_article_id']?>">
    </div>
    <?php
}

/**
 * Affiche un checkbox sans vérifier quoi que ce soit
 * @param  [array] $option [fetch de options avec un fetchAll des choix du select dans $option['option_choices'] ]
 */
function select_form_basic($option, $mandatory=false)
{
    ?>
    <div class="select_option form-group">
        <label>
            <span><?= $option['name'] ?></span>
            <button class="btn option_tooltip" type="button" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <select class="form-control">
            <option disabled <?=$mandatory==false ? 'selected' : '' ?> style="display:none">Sélectionnez l'option que vous voulez offrir !</option>
            <?php insert_select_options_no_checking($option, $mandatory); ?>
        </select>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}

/**
 * Ajoute toutes les options possibles à un select
 * @param  [array] $option [fetch de options avec un fetchAll des choix du select dans $option['option_choices'] ]
 */
function insert_select_options_no_checking($option, $mandatory)
{
    foreach($option['option_choices'] as $option_choice)
    {
        ?>
        <option <?=$mandatory == true ? 'selected' : '' ?> value="<?=$option_choice['choice_id']?>">
            <?= htmlspecialchars($option_choice['name']) . ' (' . htmlspecialchars($option_choice['price']) . '€)' ?>
        </option>
        <?php $mandatory=false;
    }
}

/**
 * Permet d'ajouter tous les liens vers les listes de participants des events
 * @param  [object] $fundation
 */
function display_fundations_participants_admin($fundation, $i)
{
    global $_CONFIG, $is_super_admin;
    $fundation_events = separate_good_bad_events(get_fundation_events($fundation->fun_id));

    $not_displayed_events_number = $is_super_admin ? " - " . count($fundation_events["not_displayed_events"]) . "anciens" : "";

    if(count($fundation_events["displayed_events"])>0) { ?>
        <div class="col-sm-4">
            <a data-toggle="collapse" href="#button_links_<?=$fundation->fun_id?>" role="button" aria-expanded="false" aria-controls="#button_links_<?=$fundation->fun_id?>"><h2><?=htmlspecialchars($fundation->name) . "(" . count($fundation_events["displayed_events"]) . ")"?></h2></a>
            <div class="collapse" id="button_links_<?=$fundation->fun_id?>">
                <?php
                foreach($fundation_events["displayed_events"] as $event)
                {
                    ?><a href="<?=$_CONFIG['public_url']?>participant_administration/participants.php?event_id=<?=$event['event_id']?>" class="btn btn-primary"><h5><?=$event['name']?></h5></a><br><br><?php
                }
                ?>
            </div>
        </div>
        <?php
        $i++;
    }
    return $i;
}

/**
 * Affiche les stats sur les promos
 * @param  [array] $promos_data [stats sur la promo]
 */
function display_promo_stats($promos_data)
{
    foreach($promos_data as $promo_data)
    {
        $promo_stats = prepare_promo_stats($promo_data);
        ?>
        <tr>
            <th class="col-sm-2"><?= $promo_stats['promo_name'] . " " . $promo_stats['site_name'] ?></th>
            <td class="col-sm-1"><?= $promo_stats['promo_count'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_quota'], 2)?>"><?= $promo_stats['pourcentage_quota'] ?></td>
            <td class="col-sm-1"><?= $promo_stats['quota'] !== null ? $promo_stats['quota'] : "infini" ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_evenement'], count($promos_data))?>"><?= $promo_stats['pourcentage_evenement'] ?></td>
            <td class="col-sm-1"><?= $promo_stats['invited_guests'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_invites'], count($promos_data))?>"><?= $promo_stats['pourcentage_invites'] ?></td>
            <td class="col-sm-1"><?= $promo_stats['bracelet_count'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_bracelet'], 2)?>"><?= $promo_stats['pourcentage_bracelet'] ?></td>
        </tr>
        <?php
    }
}

/**
 * Affiche les stats des events
 * @param  [array] $payments_stats [stats sur les moyens de payement de l'event]
 * @param  [int] $total_number   [nombre de participants actuel total]
 */
function display_payments_stats($payments_stats, $total_number)
{
    foreach($payments_stats as $payment_stats)
    {
        $pourcentage_payment = round(100 * $payment_stats['nombre'] / $total_number, 2) .'%';
        ?>
        <tr>
            <th class="col-sm-2"><?= $payment_stats['payement']?></th>
            <td class="col-sm-1"><?= $payment_stats['nombre'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($pourcentage_payment, count($payments_stats))?>"><?= $pourcentage_payment ?></td>
        </tr>
        <?php
    }
}

/**
 * Affiche les stats des 7 derniers jours
 * @param  [array] $days_stats [stats sur les 7 derniers jours]
 * @param  [int] $total_number   [nombre de participants actuel total]
 */
function display_days_stats($days_stats, $total_number)
{
    foreach($days_stats as $day_stats)
    {
        $pourcentage_day = round(100 * $day_stats['nombre'] / $total_number, 2) .'%';
        ?>
        <tr>
            <th class="col-sm-2"><?= $day_stats['day']?></th>
            <td class="col-sm-1"><?= $day_stats['nombre'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($pourcentage_day, count($days_stats))?>"><?= $pourcentage_day ?></td>
        </tr>
        <?php
    }
}

/**
 * Fonction permettant de mettre une couleur particulière sur un pourcentage
 * Il est appelée juste au moement d'écrire la classe de la cellule
 * Dans l'ordre croissant : rouge / jaune / gris / bleu / vert
 * @param  [int] $pourcentage
 * @param  [int] $number_rows [en théorie le nombre de lignes, afin de pondérer le pourcentage par raport à la moyenne. En soit, il est possible de mettre n'importe quoi afin d'ajuster certaines couleurs]
 */
function display_pourcentage_style($pourcentage, $number_rows)
{
    switch ($pourcentage)
    {
        case ('undefined'):
            break;
        case ($pourcentage<40/$number_rows):
            echo 'danger';
            break;
        case ($pourcentage<70/$number_rows):
            echo 'warning';
            break;
        case ($pourcentage<100/$number_rows):
            echo 'active';
            break;
        case ($pourcentage<175/$number_rows):
            echo 'info';
            break;
        case ($pourcentage>175/$number_rows):
            echo 'success';
            break;
    }
}

/**
 * [Bouton permettant d'afficher s'il y a des réservations en attente. S'il est affiché, il est rouge, et on a un léger détail si on clique dessus]
 * @param  [array] $participant
 */
function display_pending_reservations($participant)
{
    ?>
    <td>
    <?php
    if($participant['is_icam']==1)
    {
        $pending_reservations = get_pending_reservations($participant['event_id'], $participant['email']);
        if(!empty($pending_reservations))
        {
            if(count($pending_reservations)==1)
            {
                ?>
                <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Réservation en attente : <?=$pending_reservations[0]['price']?>€" data-content="<?=display_pending_description(json_decode($pending_reservations[0]['liste_places_options']))?>" type="button">
                    <span style="color: red" class="glyphicon glyphicon-usd option_tooltip_glyph"></span>
                </button>
                <?php
            }
            else
            {
                foreach($pending_reservations as $pending_reservation)
                {
                    update_reservation_status('A', $pending_reservation);
                }
            }
        }
        // else
        // {
            ?>
            <!-- <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="" data-content="" type="button">
                <span style="color: green" class="glyphicon glyphicon-usd option_tooltip_glyph"></span>
            </button> -->
            <?php
        // }
    }
    ?>
    </td>
    <?php
}

/**
 * Ecrit le texte affiché quand on clique sur le bouton transaction en attente
 * @param  [object] $transaction_content [Champ transactions.liste_places_options déparsé du JSON]
 */
function display_pending_description($transaction_content)
{
    echo count($transaction_content->participant_ids) >=1 ? count($transaction_content->participant_ids) . " Places <br>" : "";
    echo count($transaction_content->option_ids) >=1 ? count($transaction_content->option_ids) . " Options <br>" : "";
}

/**
 * Affiche juste les possibilités de recherche dans une fonction pour ne pas saturer une ligne, vu que c'est dans un attribut d'un bouton
 */
function display_search_possibilities()
{
    ?>
    - Prénom <br>
    - Nom <br>
    - Prénom + espace + nom <br>
    - Promo exacte <br>
    - Site exact <br>
    - Identifiant de bracelet <br>
    <?php
}

/**
 * Affiche les lignes du tableau sur les entrées
 * @param  [array] $participants [fetchALl de participants]
 */
function display_participants_rows($participants)
{
    foreach($participants as $participant)
    {
        $participant = prepare_participant_displaying($participant);
        ?>
        <tr data-participant_id=<?=$participant['participant_id']?>>
            <td><span class='badge badge-pill badge-success'><?=$participant['bracelet_identification']?></span></td>
            <td><?=$participant['prenom']?></td>
            <td><?=$participant['nom']?></td>
            <td><span class='badge badge-pill badge-info'><?=get_promo_name($participant['promo_id'])?></span></td>
            <?=display_options($participant)?>
            <?=display_guest_infos($participant)?>
            <?=display_personnal_informations($participant)?>
            <?=display_validate_button($participant)?>
        </tr>
        <?php
    }
}

/**
 * Permet d'afficher le bouton qui indique les options prises
 * @param  [array] $participant
 */
function display_options($participant)
{
    ?>
        <td>
            <?php if(!empty($participant['validated_options'])) { ?>
            <button class="btn option_tooltip options" data-container="body" data-toggle="popover" data-html="true" title="Options du participant : " data-content="<?= create_option_text($participant['validated_options']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
            <?php } ?>
        </td>
    <?php
}

/**
 * Permet d'afficher le bouton qui indique qui on invite, ou qui nous a invité
 * @param  [array] $participant
 */
function display_guest_infos($participant)
{
    global $event_id;
    ?> <td> <?php
        if($participant['is_icam'] == 1)
        {
            $guests = get_icams_guests(array('event_id' => $_GET['event_id'], 'icam_id' => $participant['participant_id']));
            if(!empty($guests)) { ?>
                <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Invités :" data-content="<?= create_guests_text($guests) ?>" type="button">
                    <?=$participant['current_promo_guest_number']?>
                </button>
            <?php }
        }
        else
        {
            $icam_data = get_icam_inviter_data($participant['participant_id']);
            if(!empty($icam_data)) { ?>
                <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-content="Invité par <?=$icam_data['prenom'] . " " . $icam_data['nom'] ?>" type="button">
                    <span class="glyphicon glyphicon-user option_tooltip_glyph"></span>
                </button>
            <?php }
        }
    ?> </td> <?php
}

/**
 * Crée le texte précisant tous les moyens de payements utilisés par l'Icam. Le premier inscrit est forcément celui de l'évènement
 * @param  [array] $payements [un array de tous les payements]
 */
function create_payements_text($payements)
{
    $payements_text = implode(" / ", $payements);
    echo $payements_text;
}

/**
 * Crée le Texte des invités d'un Icam
 * @param  [array] $guests [ce qui sort de la fonction get_icam_guests]
 */
function create_guests_text($guests)
{
    foreach($guests as $guest)
    {
        echo $guest['prenom'] . ' ' . $guest['nom'] . '<br>';
    }
}

/**
 * Permet de créer le texte sur les infos personnelles, dans un seul endroit
 * @param  [array] $participant
 */
function create_personal_informations_text($participant)
{
    ?>
    <strong>Site :</strong> <span class='badge badge-pill badge-inverse'><?=$participant['site']?></span> <br>
    <strong>Prix :</strong> <span class='badge badge-pill badge-info'><?=get_participant_option_prices($participant['participant_id']) + $participant['price']?>€</span> <br>
    <strong>Payement :</strong> <span class='badge badge-pill badge-success'><?=create_payements_text($participant['payements'])?></span> <br>
    <strong>Inscription :</strong> <span class='badge badge-pill badge-error'><?=date('d/m/Y à H:i:s', date_create_from_format('Y-m-d H:i:s', $participant['inscription_date'])->getTimestamp())?></span> <br>
    <?= isset($participant['email']) ? "<strong>Email :</strong> <span class='badge badge-pill badge-inverse'>" . $participant['email'] . "</span><br>" : "" ?>
    <?php
}

/**
 * Crée le bouton des infos personnelles, cliquer pour les voir s'afficher
 * @param  [type] $participant
 */
function display_personnal_informations($participant)
{
    $participant['payements'] = array_unique(array_merge(array($participant['payement']), array_column($participant['validated_options'], 'payement')));
    ?>
    <td>
        <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Informations supplémentaires" data-content="<?= create_personal_informations_text($participant) ?>" type="button">
            <span class="glyphicon glyphicon-eye-open option_tooltip_glyph"></span>
        </button>
    </td>
    <?php
}

/**
 * Crée le bouton permettant de faire entrer ou sortir un participant
 * @param  [type] $participant
 */
function display_validate_button($participant)
{
    ?>
    <td>
        <?= $participant['is_in'] ?
        '<button class="is_in option_tooltip btn btn-danger" data-container="body" type="button">✘</button>' :
        '<button class="is_out option_tooltip btn btn-success" data-container="body" type="button">✔</button>' ?>
    </td>
    <?php
}

/**
 * Créé le texte sur les option, permettant de savoir quelles options on a pris.
 * @param  [type] $option_choices
 */
function create_option_text($option_choices)
{
    foreach($option_choices as $option_choice)
    {
        $option_message = $option_choice['name']==null ? "" : " Choix " . $option_choice['name'];
        echo get_option_name($option_choice['option_id']) . $option_message. '<br>';
    }
}

/**
 * Permet de créer le lien amenant à la liste des participants de l'évènement
 * @param  int $event_id
 */
function display_back_to_list_button($event_id)
{
    global $_CONFIG;
    ?>
    <div class="container">
        <a class="btn btn-primary" href="<?=$_CONFIG['public_url']?>participant_administration/participants.php?event_id=<?=$event_id?>">Retour à la liste</a>
    </div>
    <?php
}
/**
 * Permet de créer le lien amenant aux entrées de l'évènement
 * @param  int $event_id
 */
function display_go_to_arrivals($event_id)
{
    global $_CONFIG;
    ?>
    <div class="container">
        <a class="btn btn-primary" href="<?=$_CONFIG['public_url']?>participant_administration/entrees.php?event_id=<?=$event_id?>">Aller aux entrées</a>
    </div>
    <?php
}

/**
 * Permet d'afficher les statistiques des options
 * @param  [array] $option_stats [retour de la fonction get_option_stats]
 */
function display_option_stats($option_stats)
{
    $i=0;
    foreach($option_stats as $option_stats)
    {
        $i++;
        ?>
        <div class="col-sm-6">
            <h3 class="text-center"><?=$option_stats['name']?></h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Choix</th>
                        <th>Nombre de participants</th>
                        <th>Quota</th>
                        <th>Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php display_option_choices_stats($option_stats['choices']); ?>
                    <tr>
                        <td><?=$option_stats['type'] == 'Select' ? 'Total' : $option_stats['name']?></td>
                        <td><?=$option_stats['option_count']?></td>
                        <td><?= $option_stats['quota'] !== null ? $option_stats['quota'] : "infini" ?></td>
                        <td class="<?=display_pourcentage_style($option_stats['pourcentage_option'], 2)?>"><?=$option_stats['pourcentage_option']?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        if($i%2==0)
        {
            echo '</div><div class="row">';
        }
    }
}

/**
 * [display_option_choices_stats description]
 * @param  [array] $choices_stats [statistiques et informations sur les choix pris]
 */
function display_option_choices_stats($choices_stats)
{
    foreach($choices_stats as $choice_stats)
    {
        $choice_stats['pourcentage_choice'] = $choice_stats['quota'] !=0 ? round(100 * $choice_stats['choice_count'] / $choice_stats['quota'], 2) . '%' : "undefined";
        ?>
        <tr>
            <td><?=$choice_stats['name']?></td>
            <td><?=$choice_stats['choice_count']?></td>
            <td><?=$choice_stats['quota'] !== null ? $choice_stats['quota'] : "infini" ?></td>
            <td class="<?=display_pourcentage_style($choice_stats['pourcentage_choice'], 2)?>"><?=$choice_stats['pourcentage_choice']?></td>
        </tr>
        <?php
    }
}
