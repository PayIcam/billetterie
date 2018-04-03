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
    ?><span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value=">" />
            </form>
        </span><?php
    }}
    if(!function_exists('prev_page')) {
    function prev_page($current_page, $rows_per_page)
    {
        $wanted_page = $current_page-1;
        $page_text='&page=' . $wanted_page;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
    ?><span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value="<" />
            </form>
        </span><?php
    }}
    if(!function_exists('last_page')) {
    function last_page($current_page, $rows_per_page, $total_number_pages)
    {
        $page_text='&page=' . $total_number_pages;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
    ?><span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value=">>>" />
            </form>
        </span><?php
    }}
    if(!function_exists('first_page')) {
    function first_page($current_page, $rows_per_page)
    {
        $page_text='&page=' . 1;
        $row_text ='&rows=' . $rows_per_page;
        $event_text = '?event_id=' . $_GET['event_id'];
        $link = ($rows_per_page == 25) ? "participants.php" . $event_text.$page_text : "participants.php" . $event_text.$page_text.$row_text;
    ?><span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value="<<<" />
            </form>
        </span><?php
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

function display_liste_head($specification="", $id=true, $status=false, $price=true, $email=false, $telephone=true, $guest_number=false, $options=true, $edit=true, $add_guest=true, $bracelet=true, $date_inscription=true, $date_payement=false, $pending_indicator=true, $guest_info=true)
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
    if(!$Auth->hasRole('admin'))
    {
        $add_guest = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $add_guest = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $telephone = false;
        $guest_number = false;
        $pending_indicator = false;
        $edit = false;
        $add_guest = false;
    }
    elseif($specification == 'link_icam')
    {
        $add_guest = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $telephone = false;
        $guest_number = false;
        $add_guest = false;
        $pending_indicator = false;
    }
    ?>
        <?php if($id) { ?> <th scope="col">ID</th> <?php } ?>
        <?php if($status) { ?> <th scope="col">Status</th> <?php } ?>
        <th scope="col">Prénom</th>
        <th scope="col">Nom</th>
        <?php if($price) { ?> <th scope="col">Prix</th> <?php } ?>
        <th scope="col">Promo</th>
        <?php if($bracelet) { ?> <th scope="col">Bracelet</th> <?php } ?>
        <!-- <th scope="col">Créneau</th> -->
        <!-- <th scope="col">Tickets Boissons</th> -->
        <?php if($email) { ?> <th scope="col">Email</th> <?php } ?>
        <?php if($telephone) { ?> <th scope="col">Telephone</th> <?php } ?>
        <?php if($date_inscription) { ?> <th scope="col">Date Inscription</th> <?php } ?>
        <?php if($date_payement) { ?> <th scope="col">Date Payement</th> <?php } ?>
        <?php if($guest_number) { ?> <th scope="col">Nombre d'invités</th> <?php } ?>
        <?php if($options) { ?> <th scope="col">Options</th> <?php } ?>
        <?php if($guest_info) { ?> <th scope="col">Invités</th> <?php } ?>
        <?php if($pending_indicator) { ?> <th scope="col">Attente</th> <?php } ?>
        <?php if($edit) { ?> <th scope="col">Editer</th> <?php } ?>
        <?php if($add_guest) { ?> <th scope="col">Ajouter invité</th> <?php } ?>
    <?php
}

function create_option_text($options)
{
    foreach($options as $option)
    {
        $select_message = $option['option_details']!=null ? json_decode($option['option_details'])->select_option : "";
        $select_message = $select_message != "" ? " Choix " . $select_message : "";
        echo get_option_name($option['option_id']) . $select_message . '<br>';
    }
}
function display_options($participant)
{
    ?>
        <td>
            <?php if(!empty($participant['validated_options'])) { ?>
            <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Options du participant : " data-content="<?= create_option_text($participant['validated_options']) ?>" type="button">
                <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
            </button>
            <?php } ?>
        </td>
    <?php
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

function create_guests_text($guests)
{
    foreach($guests as $guest)
    {
        echo $guest['prenom'] . ' ' . $guest['nom'] . '<br>';
    }
}

function display_guest_infos($participant)
{
    global $event_id;
    ?> <td> <?php
        if($participant['is_icam'] == 1)
        {
            $guests = get_guests_data($participant['participant_id']);
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
function link_to_guest_addition($participant)
{
    $event_id = $_GET['event_id'];
    ?>
    <td>
        <?php if($participant['is_icam']==1) { ?>
        <a class="btn btn-primary" href="ajout_participant.php?event_id=<?=$event_id?>&icam_id=<?=$participant['participant_id']?>">
            <span class="glyphicon glyphicon-plus"></span>
        </a>
        <?php } ?>
    </td>
    <?php
}

function display_promo($promo)
{
    $promo_still_student = get_promo_status($promo);
    $class = $promo == 'Invités' ? 'warning' : ($promo_still_student==1 ? 'info' : 'primary');
    ?>
    <td class="<?=$class?>"> <?=$promo?> </td>
    <?php
}

function display_participant_info($participant, $specification="", $id=true, $status=false, $price=true, $email=false, $telephone=true, $guest_number=false, $options=true, $edit=true, $add_guest=true, $bracelet=true, $date_inscription=true, $date_payement=false, $pending_indicator=true, $guest_info=true)
{
    global $Auth;
    if(!$Auth->hasRole('admin'))
    {
        $add_guest = false;
    }

    if($specification == 'info_icam')
    {
        $edit = false;
        $add_guest = false;
    }
    elseif($specification == 'info_invite')
    {
        $email = false;
        $telephone = false;
        $guest_number = false;
        $pending_indicator = false;
        $edit = false;
        $add_guest = false;
    }
    elseif($specification == 'link_icam')
    {
        $add_guest = false;
    }
    elseif($specification == 'link_invite')
    {
        $email = false;
        $telephone = false;
        $guest_number = false;
        $add_guest = false;
        $pending_indicator = false;
    }
    ?>
    <tr>
        <?= $id ? "<td>" . $participant['participant_id'] . "</td>" : "" ?>
        <?= $status ? "<td>" . $participant['status'] . "</td>" : "" ?>
        <td class="prenom"><?= htmlspecialchars($participant['prenom']) ?></td>
        <td class="nom"><?= htmlspecialchars($participant['nom']) ?></td>
        <?= $price ? "<td><span style='background-color: #3a87ad' class='badge badge-pill badge-info'>" . $participant['price'] . "€</span></td>" : "" ?>
        <?= display_promo($participant['promo']); ?>
        <?= $bracelet ? "<td class='bracelet_identification'>" . $participant['bracelet_identification'] . "</td>" : "" ?>
        <?= $email ? "<td>" . $participant['email'] . "</td>" : "" ?>
        <?= $telephone ? "<td>" . htmlspecialchars($participant['telephone']) . "</td>" : "" ?>
        <?= $date_inscription ? "<td>" . $participant['inscription_date'] . "</td>" : "" ?>
        <?= $guest_number ? "<td>" . $participant['current_promo_guest_number'] . "</td>" : "" ?>
        <?= $options ? display_options($participant) : "" ?>
        <?= $guest_info ? display_guest_infos($participant) : "" ?>
        <?= $pending_indicator ? display_pending_reservations($participant) : "" ?>
        <?= $edit ? link_to_edit_reservation($participant) : "" ?>
        <?= $add_guest ? link_to_guest_addition($participant) : "" ?>
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