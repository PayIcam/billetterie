<?php

function display_liste_head($specification="", $id_set=true, $mail_set=true, $telephone_set=true, $nombre_invites_set=true, $infos_set=true, $editer_set=true, $ajouter_invite_set=true)
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

    <?php if($id_set) { ?> <th scope="col">ID</th> <?php } ?>
    <th scope="col">Nom</th>
    <th scope="col">Prénom</th>
    <th scope="col">Promo</th>
    <th scope="col">Bracelet</th>
    <th scope="col">Créneau</th>
    <th scope="col">Tickets Boissons</th>
    <?php if($mail_set) { ?> <th scope="col">Email</th> <?php } ?>
    <?php if($telephone_set) { ?> <th scope="col">Telephone</th> <?php } ?>
    <th scope="col">Date Inscription</th>
    <?php if($nombre_invites_set) { ?> <th scope="col">Nombre d'invités</th> <?php } ?>
    <?php if($infos_set) { ?> <th scope="col">Infos</th> <?php } ?>
    <?php if($editer_set) { ?> <th scope="col">Editer</th> <?php } ?>
    <?php if($ajouter_invite_set) { ?> <th scope="col">Ajouter un invité </th> <?php } ?>

    <?php
}

function display_participant_info($participant, $specification="", $secured=false, $id_set=true, $mail_set=true, $telephone_set=true, $nombre_invites_set=true, $infos_set=true, $editer_set=true, $ajouter_invite_set=true)
{
/**
 *
 * Cette fonction va créer une ligne du corps du tableau, et est appelé dans une boucle en général, affichant donc autant de lignes que souhaité.
 *
 * Elle prend un seul paramètre obligatoire : $participant, qui contient l'intégralité des infos sur le participant
 *
 * Le principe est exactement le même que pour create_liste_head au niveau des paramètres facultatifs. Il y a juste un léger ajout: si on est dans le dossier secured, il faut passer le paramètre $secured à true
 *
 */
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

        <?php if($id_set) { ?>
            <td> <?php echo htmlspecialchars($participant['id']) ?> </td>
        <?php } ?>
        <td><?php echo htmlspecialchars($participant['nom']) ?></td>
        <td><?php echo htmlspecialchars($participant['prenom']) ?></td>
        <td><?php if ($participant['promo']!=''){echo htmlspecialchars($participant['promo']);}else{echo 'Invité';} ?></td>
        <td><?php echo four_chars_bracelet_id($participant['bracelet_id']) ?></td>
        <td><?php ajustement_creneau($participant['plage_horaire_entrees']) ?></td>
        <td><span class="badge badge-pill badge-info"><?php echo htmlspecialchars($participant['tickets_boisson']) ?></span></td>
        <?php if($mail_set) { ?>
            <td><?php echo htmlspecialchars($participant['email']) ?></td>
        <?php } ?>
        <?php if($telephone_set) { ?>
            <td><?php echo htmlspecialchars($participant['telephone']) ?></td>
        <?php } ?>
        <td><?php echo htmlspecialchars($participant['inscription']) ?></td>
        <?php if($nombre_invites_set) { ?>
            <td><?php echo htmlspecialchars($participant['nombre_invites_et_total']); ?></td>
        <?php } ?>
        <?php if($infos_set) { ?>
            <td><a data-container="body" data-toggle="popover" data-placement="top" data-content="<?php diner_conference($participant); ?>"><i class="fas fa-info-circle fa-lg"></i></a></td>
        <?php } ?>
        <!-- ___ Bouton Editer un invité ___ -->
        <?php if($editer_set) {
        $link = ($secured) ? '../edit.php' : 'edit.php'; ?>
            <td> <form method=get action="<?php echo $link ?>" >
                    <input type="hidden" value="<?php echo htmlspecialchars($participant['id']); ?>"  name=edit_id>
                    <button class="btn btn-primary" type="submit" title="Editer l'invité <?php echo htmlspecialchars($participant['prenom'].' '.$participant['nom'])?>"> Editer
                    </button>
                </form>
            </td>
        <?php } ?>
        <!-- ___ Bouton ajouter un invité ___
        Affiché seulement pour un Icam selon son nombre d'invités -->
        <?php if($ajouter_invite_set) { ?>
            <td>
                <?php if ($participant['is_icam']==1 )
                { ?>
                    <form method=get action='secured/ajouter_invite.php'>
                        <input type="hidden" value="<?php echo htmlspecialchars($participant['id']); ?>" name=add_id>
                        <button type="submit" class="btn btn-primary" title="Ajouter un invité à <?php echo htmlspecialchars($participant['prenom'].' '.$participant['nom'])?>">Nouvel invité</button>
                    </form>
                <?php } ?>
            </td>
        <?php } ?>

    </tr>

    <?php
}
function tableau_une_ligne($participant, $specification="", $secured=false, $id_set=true, $mail_set=true, $telephone_set=true, $nombre_invites_set=true, $infos_set=true, $editer_set=true, $ajouter_invite_set=true)
{
    ?>
    <div class="container">
        <section class="row" id="tableau">
            <table class="table table-striped">
                <thead>
                    <?php display_liste_head($specification); ?>
                </thead>
                <tbody>
                <?php display_participant_info($participant, $specification, $secured, $id_set, $mail_set, $telephone_set, $nombre_invites_set, $infos_set, $editer_set, $ajouter_invite_set); ?>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}

function change_pages($current_page, $rows_per_page, $total_number_pages)
{
    /**
     *
     * Cette fonction va créer les butons servant à changer de pages.
     *
     *En paramètres, on lui donne le numéro de page actuel, le nombre de lignes de la page actuelle, et le nombre total de pages .
     *
     * Elle ne va ajouter que les boutons nécessaires (si on est à la page 1, il ne faut pas essayer de passer à la page 0 => pas de bouton retour à la page précédente).
     *
     * On fait appel à 4 fonctions faisant le travail de façon analogue les unes après les autres, après les avoir définies dans la fonction même.
     *
     * Chaque bouton est un objet de type <form> marchant en POST, on lui passe en GET par l'url les infos sur la page, et sur le nombre de lignes par page.
     *
     * Rien n'est retourné, le code HTML est écrit directement.
     */

    if(!function_exists('next_page')) {
    function next_page($current_page, $rows_per_page)
    {
        $index = 'index.php';
        $next_page = $current_page+1;
        $page_text = '?page='.$next_page;
        $row_text ='&rows=' . $rows_per_page;
        $link = ($rows_per_page == 25) ? $index.$page_text : $index.$page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?php echo $link; ?>">
                <?php if (isset($_POST['recherche'])) {echo ('<input type="hidden" name="recherche" value="'). $_POST['recherche']. '" /> ';} ?>
                <input type="submit" value=">" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('prev_page')) {
    function prev_page($current_page, $rows_per_page)
    {
        $index = 'index.php';
        $prev_page = $current_page-1;
        $page_text = '?page='.$prev_page;
        $row_text ='&rows=' . $rows_per_page;
        $link = ($rows_per_page == 25) ? $index.$page_text : $index.$page_text.$row_text;
    ?>

    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?php echo $link; ?>">
                <?php if (isset($_POST['recherche'])) {echo ('<input type="hidden" name="recherche" value="'). $_POST['recherche']. '" /> ';} ?>
                <input type="submit" value="<" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('last_page')) {
    function last_page($current_page, $rows_per_page, $total_number_pages)
    {
        $index = 'index.php';
        $page_text = '?page='.$total_number_pages;
        $row_text ='&rows=' . $rows_per_page;
        $link = ($rows_per_page == 25) ? $index.$page_text : $index.$page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?php echo $link; ?>">
                <?php if (isset($_POST['recherche'])) {echo ('<input type="hidden" name="recherche" value="'). $_POST['recherche']. '" /> ';} ?>
                <input type="submit" value=">>>" />
            </form>
        </span>
    </div>
    <?php
    }}
    if(!function_exists('first_page')) {
    function first_page($current_page, $rows_per_page)
    {
        $index = 'index.php';
        $page_text = '?page='.'1';
        $row_text ='&rows=' . $rows_per_page;
        $link = ($rows_per_page == 25) ? $index.$page_text : $index.$page_text.$row_text;
    ?>
    <div class="change_page">
        <span class="change_page_text">
            <form method="post" action="<?php echo $link; ?>">
                <?php if (isset($_POST['recherche'])) {echo ('<input type="hidden" name="recherche" value="'). $_POST['recherche']. '" /> ';} ?>
                <input type="submit" value="<<<" />
            </form>
        </span>
    </div>
    <?php
    }}

    if ($current_page>2)
    {
        first_page($current_page, $rows_per_page);
    }
    if ($current_page>1)
    {
        prev_page($current_page, $rows_per_page);
    }
    if ($total_number_pages >1 and $current_page <$total_number_pages)
    {
        next_page($current_page, $rows_per_page);
    }
    if($current_page<$total_number_pages-1)
    {
        last_page($current_page, $rows_per_page, $total_number_pages);
    }
}
function change_number_rows($rows_per_page)
{
?>
<div class="change_number_rows">
    <form method="get" action="index.php">
        <label for ="#change_rows"> Nombre de lignes par page : </label> <br/>
        <select class="custom-select mr-sm-2" id="change_rows" name="rows">
            <option <?php if($rows_per_page==10){echo 'selected';} ?> > 10 </option>
            <option <?php if($rows_per_page==15){echo 'selected';} ?>> 15 </option>
            <option <?php if($rows_per_page==20){echo 'selected';} ?>> 20 </option>
            <option <?php if($rows_per_page==25){echo 'selected';} ?>> 25 </option>
            <option <?php if($rows_per_page==50){echo 'selected';} ?>> 50 </option>
            <option <?php if($rows_per_page==100){echo 'selected';} ?>> 100 </option>
            <option <?php if($rows_per_page==250){echo 'selected';} ?>> 250 </option>
        </select>
    </form>
</div>
<?php }

function previous_page()
{
    if(isset($_SERVER['HTTP_REFERER']) and !isset($noreturn))
    {
    ?>
    <form method=post action="<?php echo $_SERVER['HTTP_REFERER']?>">
        <input type ="hidden" name="retour_page_precedente" value=<?php echo 1;?> />
        <input type="submit" value="Retour à la page précédente"/>
    </form>
    <?php
    }
}
function heures_inscription_head($data_per_hour, $tr_class, $th_class)
{
    foreach($data_per_hour as $hour_group)
    ?>
    <tr class="<?php echo $tr_class;?>">
        <th class="<?php echo $th_class;?>">
            <?php echo $data_per_hour['day']. '/01/18 ' . $data_per_hour['hour_group']; ?>
        </th>
    </tr>
    <?php
}