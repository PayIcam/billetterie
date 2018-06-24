<?php

/**
 * A utiliser quand on insère les promos ajoutées dans l'edit de l'évènement.
 * La classe est utilisée pour afficher en success celles qui sont validées, et en error celles qui sont enlevées.
 * @param  array $promos_specifications [fetchAll de la table pss]
 * @param  string $class                 [n'importe quel theme BS de couleur pour les rows]
 */
function insert_event_accessibility_rows($promos_specifications, $class="")
{
    static $numero = 1;
    foreach($promos_specifications as $promo_specifications)
    {
        ?>
        <tr class="<?=$class?>">
            <th class="col-sm-1" scope="col"><?= $numero ?></th>
            <td class="col-sm-1" scope="col"><?= htmlspecialchars(get_site_name($promo_specifications['site_id'])) ?></td>
            <td class="col-sm-1" scope="col"><?= htmlspecialchars(get_promo_name($promo_specifications['promo_id'])) ?></td>
            <td class="col-sm-2" scope="col"><?= htmlspecialchars($promo_specifications['price']) . '€'?></td>
            <td class="col-sm-1" scope="col"><?= htmlspecialchars($promo_specifications['quota'])?></td>
            <td class="col-sm-2" scope="col"><?= htmlspecialchars($promo_specifications['guest_number'])?></td>
            <td class="col-sm-3" scope="col"><button type="button" class="btn btn-danger creation_button_icons"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}

/**
 * Même délire qu'au dessus mais pour les promos qui ont accès à l'évent
 * @param  array $promo_options [fetchAll de promo_site_has_options]
 */
function insert_option_accessibility_rows($promo_options)
{
    $numero = 1;
    foreach($promo_options as $promo_option)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= htmlspecialchars(get_site_name($promo_option['site_id'])) ?></td>
            <td><?= htmlspecialchars(get_promo_name($promo_option['promo_id'])) ?></td>
            <td><button type="button" class="btn btn-danger creation_button_icons"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}

/**
 * Encore pareil, mais il s'agit ici de renseigner les rows pour les choix des selects
 * @param  array $option_choices [fetchAll de option_choices]
 */
function insert_option_select_rows($option_choices)
{
    $numero = 1;
    foreach($option_choices as $option_choice)
    {
        ?>
        <tr data-choice_id=<?=$option_choice['choice_id']?>>
            <th><?= $numero ?></th>
            <td><?= htmlspecialchars($option_choice['name']) ?></td>
            <td><?= htmlspecialchars($option_choice['price']).'€' ?></td>
            <td><?= htmlspecialchars($option_choice['quota']) ?></td>
            <td><button type="button" class="btn btn-danger creation_button_icons"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}

function display_fundations_events_admin($fundation)
{
    global $_CONFIG;
    $fundation_events = get_fundation_events($fundation->fun_id);
    ?>
    <div class="col-sm-4">
        <a data-toggle="collapse" href="#button_links_<?=$fundation->fun_id?>" role="button" aria-expanded="false" aria-controls="#button_links_<?=$fundation->fun_id?>"><h2><?=htmlspecialchars($fundation->name) . '(' . count($fundation_events) . ')'?></h2></a>
        <div class="collapse" id="button_links_<?=$fundation->fun_id?>">
            <a href="new_ticketing.php?fundation_id=<?=$fundation->fun_id?>" class="btn btn-success"><h5>Créer une billetterie</h5></a><br><br>
            <?php
            foreach($fundation_events as $event)
            {
                if(!event_is_too_old($event))
                {
                    ?><a href="<?=$_CONFIG['public_url']?>event_administration/edit_ticketing.php?event_id=<?=$event['event_id']?>" class="btn btn-primary"><h5><?=$event['name']?></h5></a><br><br><?php
                }
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Fonction qui permet d'ajouter les choix dans un select,
 * @param  array $option_choices [fetchAll de option_choices]
 */
function insert_select_options($option_choices, $is_mandatory = 0)
{
    $compteur=0;
    foreach($option_choices as $option_choice)
    {
        ?>
        <option value="<?= htmlspecialchars($option_choice['choice_id']) ?>" <?=($is_mandatory==1 and $compteur==0) ? 'selected' : ''?> >
            <?= htmlspecialchars($option_choice['name']) . '(' . htmlspecialchars($option_choice['price']) . '€)' ?>
        </option>
        <?php
        $compteur++;
    }
}
