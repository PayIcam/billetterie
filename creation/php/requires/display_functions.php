<?php

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

function insert_option_select_rows($option_specifications)
{
    $numero = 1;
    foreach($option_specifications as $option_specification)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= htmlspecialchars($option_specification->name) ?></td>
            <td><?= htmlspecialchars($option_specification->price).'€' ?></td>
            <td><?= htmlspecialchars($option_specification->quota) ?></td>
            <td><button type="button" class="btn btn-danger creation_button_icons"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}

function display_event_admin($event)
{
    global $_CONFIG;
    ?>
        <a href="<?=$_CONFIG['public_url']?>creation/edit_ticketing.php?event_id=<?=$event['event_id']?>" class="btn btn-primary"><h5><?=$event['name']?></h5></a><br><br>
    <?php
}

function display_fundations_events_admin($fundation)
{
    ?>
    <div class="col-sm-4">
        <a data-toggle="collapse" href="#button_links_<?=$fundation->fun_id?>" role="button" aria-expanded="false" aria-controls="#button_links_<?=$fundation->fun_id?>"><h2><?=htmlspecialchars($fundation->name)?></h2></a>
        <div class="collapse" id="button_links_<?=$fundation->fun_id?>">
            <a href="new_ticketing.php?fundation_id=<?=$fundation->fun_id?>" class="btn btn-success"><h5>Créer une billetterie</h5></a><br><br>
            <?php
            foreach(get_fundations_events($fundation->fun_id) as $event)
            {
                display_event_admin($event);
            }
            ?>
        </div>
    </div>
    <?php
}