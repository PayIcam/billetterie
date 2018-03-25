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
        $page_text = '?page='.$current_page+1;
        $row_text ='&rows=' . $rows_per_page;
        $link = "participants.php" . ($rows_per_page == 25) ? $page_text : $page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value=">" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('prev_page')) {
    function prev_page($current_page, $rows_per_page)
    {
        $page_text = '?page='.$current_page-1;
        $row_text ='&rows=' . $rows_per_page;
        $link = "participants.php" . ($rows_per_page == 25) ? $page_text : $page_text.$row_text;
    ?>

    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value="<" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('last_page')) {
    function last_page($current_page, $rows_per_page, $total_number_pages)
    {
        $page_text = '?page='.$total_number_pages;
        $row_text ='&rows=' . $rows_per_page;
        $link = "participants.php" . ($rows_per_page == 25) ? $page_text : $page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value=">>>" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('first_page')) {
    function first_page($current_page, $rows_per_page)
    {
        $page_text = '?page='.'1';
        $row_text ='&rows=' . $rows_per_page;
        $link = "participants.php" . ($rows_per_page == 25) ? $page_text : $page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?= $link ?>">
                <?= isset($_POST['recherche']) ? '<input type="hidden" name="recherche" value="' . $_POST['recherche'] . '" > ' : "" ?>
                <input type="submit" value="<<<" />
            </form>
        </span>
    </div>
    <?php
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

function display_liste_head($specification="", $id=true, $status=false, $price=true, $email=true, $telephone=true, $guest_number=true, $options=true, $edit=false, $add_guest=false, $bracelet=false, $date_inscription=true, $date_payement=false)
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
    }
    ?>
        <?php if($id) { ?> <th scope="col">ID</th> <?php } ?>
        <?php if($status) { ?> <th scope="col">Status</th> <?php } ?>
        <th scope="col">Nom</th>
        <th scope="col">Prénom</th>
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
        <?php if($edit) { ?> <th scope="col">Editer</th> <?php } ?>
        <?php if($add_guest) { ?> <th scope="col">Ajouter un invité </th> <?php } ?>
    <?php
}

function display_participant_info($participant, $specification="", $id=true, $status=false, $price=true, $email=true, $telephone=true, $guest_number=true, $options=true, $edit=false, $add_guest=false, $bracelet=false, $date_inscription=true, $date_payement=false)
{
    if($specification == 'info_icam')
    {
        $editer_set = false;
        $ajouter_invite_set = false;
    }
    elseif($specification == 'info_invite')
    {
        $mail_set = false;
        $telephone_set = false;
        $nombre_invites_set = false;
        $editer_set = false;
        $ajouter_invite_set = false;
    }
    elseif($specification == 'link_icam')
    {
        $ajouter_invite_set = false;
    }
    elseif($specification == 'link_invite')
    {
        $mail_set = false;
        $telephone_set = false;
        $nombre_invites_set = false;
        $ajouter_invite_set = false;
    }
    ?>
    <tr>
        <?= $id ? "<td>" . $participant['participant_id'] . "</td>" : "" ?>
        <?= $status ? "<td>" . $participant['status'] . "</td>" : "" ?>
        <td><?= htmlspecialchars($participant['nom']) ?></td>
        <td><?= htmlspecialchars($participant['prenom']) ?></td>
        <?= $price ? "<td><span style='background-color: #3a87ad' class='badge badge-pill badge-info'>" . $participant['price'] . "€</span></td>" : "" ?>
        <td><?= htmlspecialchars($participant['promo']) ?></td>
        <?= $email ? "<td>" . $participant['email'] . "</td>" : "" ?>
        <?= $telephone ? "<td>" . htmlspecialchars($participant['telephone']) . "</td>" : "" ?>
        <?= $date_inscription ? "<td>" . $participant['inscription_date'] . "</td>" : "" ?>
        <?= $guest_number ? "<td>" . $participant['current_promo_guest_number'] . "</td>" : "" ?>
        <?= $options ? "<td><span class='glyphicon glyphicon-info-sign'></span></td>" : "" ?>
    </tr>
    <?php
}