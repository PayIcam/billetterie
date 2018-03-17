<?php

function insert_as_select_option($array_to_insert)
{
    foreach ($array_to_insert as $element)
    {
        echo '<option>'. $element[0] .'</option>';
    }
}

function insert_event_accessibility_rows($promos_specifications)
{
    $numero = 1;
    foreach($promos_specifications as $promo_specifications)
    {
        ?>
        <tr>
            <th><?= $numero ?></th>
            <td><?= get_site_name($promo_specifications['site_id']) ?></td>
            <td><?= get_promo_name($promo_specifications['promo_id']) ?></td>
            <td><?= $promo_specifications['price'] . '€'?></td>
            <td><?= $promo_specifications['quota']?></td>
            <td><?= $promo_specifications['guest_number']?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
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
            <td><?= get_site_name($promo_option['site_id']) ?></td>
            <td><?= get_promo_name($promo_option['promo_id']) ?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
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
            <td><?= $option_specification->name ?></td>
            <td><?= $option_specification->price.'€' ?></td>
            <td><?= $option_specification->quota ?></td>
            <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
        </tr>
        <?php
        $numero+=1;
    }
}