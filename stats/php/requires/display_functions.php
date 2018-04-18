<?php

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

function display_liste_head($specification="", $id=true, $status=false, $personnal_infos=true, $options=true, $edit=true, $additions=true, $bracelet=true, $date_payement=false, $pending_indicator=true, $guest_info=true)
{
/**
 *
 * Cette fonction va créer la structure du tableau.
 *
 * Elle marche en ne sécifiant aucun argument, alors tout sera affiché
 * Sinon, spécifier des arguements permet d'enlever un champ particulier
 *
 * En particulier, certains arguments permettent de ne pas tout retapper, tout étant géré dans la fonction
 *
 */
    global $Auth;
    if(!$Auth->hasRole('super-admin'))
    {
        $id = false;
    }
    if(!$Auth->hasRole('admin'))
    {
        $additions = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $additions = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $telephone = false;
        $guest_number = false;
        $pending_indicator = false;
        $edit = false;
        $additions = false;
    }
    elseif($specification == 'link_icam')
    {
        $additions = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $telephone = false;
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
    <?php
}

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
function links_to_various_addition($participant)
{
    $event_id = $_GET['event_id'];
    ?>
    <td>
        <?php if($participant['is_icam']==1) { ?>
        <a class="btn btn-primary" href="ajout_participant.php?event_id=<?=$event_id?>&icam_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-plus"></span>
        </a>
        <?php } ?>
        <a class="btn btn-primary" href="ajout_options.php?event_id=<?=$event_id?>&participant_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-gift"></span>
        </a>
    </td>
    <?php
}

function display_promo($promo)
{
    $promo_still_student = get_promo_status($promo);
    $class = $promo == 'Invités' ? 'warning' : ($promo_still_student==1 ? 'success' : 'info');
    ?>
    <td class="<?=$class?>"> <?=$promo?> </td>
    <?php
}

function display_participant_info($participant, $specification="", $id=true, $status=false, $options=true, $edit=true, $additions=true, $bracelet=true, $personnal_infos=true, $date_payement=false, $pending_indicator=true, $guest_info=true)
{
    global $Auth;
    if(!$Auth->hasRole('super-admin'))
    {
        $id = false;
    }
    if(!$Auth->hasRole('admin'))
    {
        $additions = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $additions = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $telephone = false;
        $pending_indicator = false;
        $edit = false;
        $additions = false;
    }
    elseif($specification == 'link_icam')
    {
        $additions = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $telephone = false;
        $additions = false;
        $pending_indicator = false;
    }
    ?>
    <tr>
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
    </tr>
    <?php
}

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

function checkbox_form_basic($option)
{
    ?>
    <div class="checkbox_option form-check">
        <input class="form-check-input has_option" name="has_option" type="checkbox" value="<?=$option['specifications']->scoobydoo_article_id?>" >
        <label class="form-check-label">
            <span class="option_name"><?= htmlspecialchars($option['name']) ?></span>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" title="Description de l'option : " data-content="<?= htmlspecialchars($option['description']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
        </label>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
        <input type="hidden" class="option_article_id" name="option_article_id" value="<?=$option['specifications']->scoobydoo_article_id?>">
    </div>
    <?php
}
function select_form_basic($option)
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
            <option disabled selected style="display:none">Sélectionnez l'option que vous voulez offrir !</option>
            <?php insert_select_options_no_checking($option); ?>
        </select>
        <input type="hidden" name="option_id" value="<?=$option['option_id']?>">
    </div>
    <?php
}

function insert_select_options_no_checking($option)
{
    foreach($option['specifications'] as $option_specification)
    {
        ?>
        <option value="<?=$option_specification->name?>">
            <?= htmlspecialchars($option_specification->name) . ' (' . htmlspecialchars($option_specification->price) . '€)' ?>
        </option>
        <?php
    }
}

function display_participants_admin($event)
{
    global $_CONFIG;
    ?>
        <a href="<?=$_CONFIG['public_url']?>stats/participants.php?event_id=<?=$event['event_id']?>" class="btn btn-primary"><h5><?=$event['name']?></h5></a><br><br>
    <?php
}
function display_fundations_participants_admin($fundation)
{
    ?>
    <div class="col-sm-4">
        <a data-toggle="collapse" href="#button_links_<?=$fundation->fun_id?>" role="button" aria-expanded="false" aria-controls="#button_links_<?=$fundation->fun_id?>"><h2><?=htmlspecialchars($fundation->name)?></h2></a>
        <div class="collapse" id="button_links_<?=$fundation->fun_id?>">
            <?php
            foreach(get_fundations_events($fundation->fun_id) as $event)
            {
                display_participants_admin($event);
            }
            ?>
        </div>
    </div>
    <?php
}

function display_back_to_list_button($event_id)
{
    global $_CONFIG;
    ?>
    <div class="container">
        <a class="btn btn-primary" href="<?=$_CONFIG['public_url']?>stats/participants.php?event_id=<?=$event_id?>">Retour à la liste</a>
    </div>
    <?php
}
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
            <td class="col-sm-1"><?= $promo_stats['quota'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_evenement'], count($promos_data))?>"><?= $promo_stats['pourcentage_evenement'] ?></td>
            <td class="col-sm-1"><?= $promo_stats['invited_guests'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_invites'], count($promos_data))?>"><?= $promo_stats['pourcentage_invites'] ?></td>
            <td class="col-sm-1"><?= $promo_stats['bracelet_count'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($promo_stats['pourcentage_bracelet'], 2)?>"><?= $promo_stats['pourcentage_bracelet'] ?></td>
        </tr>
        <?php
    }
}

function display_payments_stats($payments_stats, $total_number)
{
    foreach($payments_stats as $payment_stats)
    {
        $pourcentage_payment = round(100 * $payment_stats['nombre'] / $total_number, 2) .'%';
        ?>
        <tr>
            <th class="col-sm-3"><?= $payment_stats['payement']?></th>
            <td class="col-sm-1"><?= $payment_stats['nombre'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($pourcentage_payment, count($payments_stats))?>"><?= $pourcentage_payment ?></td>
        </tr>
        <?php
    }
}

function display_days_stats($days_stats, $total_number)
{
    foreach($days_stats as $day_stats)
    {
        $pourcentage_day = round(100 * $day_stats['nombre'] / $total_number, 2) .'%';
        ?>
        <tr>
            <th class="col-sm-3"><?= $day_stats['day']?></th>
            <td class="col-sm-1"><?= $day_stats['nombre'] ?></td>
            <td class="col-sm-1 <?=display_pourcentage_style($pourcentage_day, count($days_stats))?>"><?= $pourcentage_day ?></td>
        </tr>
        <?php
    }
}

function display_pourcentage_style($pourcentage, $number_rows)
{
    switch ($pourcentage)
    {
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

function display_pending_reservations($participant)
{
    ?>
    <td>
    <?php
    if($participant['is_icam']==1)
    {
        if(count(get_pending_reservations($participant['event_id'], $participant['email'])) >=1 )
        {
            ?>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="" data-content="" type="button">
                <span style="color: red" class="glyphicon glyphicon-usd option_tooltip_glyph"></span>
            </button>
            <?php
        }
        else
        {
            ?>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="" data-content="" type="button">
                <span style="color: green" class="glyphicon glyphicon-usd option_tooltip_glyph"></span>
            </button>
            <?php
        }
    }
    ?>
    </td>
    <?php
}
