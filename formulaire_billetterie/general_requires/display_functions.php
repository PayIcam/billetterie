<?php

function insert_select_options($option_specifications, $is_mandatory = 0)
{
    $compteur=0;
    foreach($option_specifications as $option_specification)
    {
        ?>
        <option value="<?= $option_specification->name ?>" <?=($is_mandatory==1 and $compteur==0) ? 'selected' : ''?> >
            <?= $option_specification->name . '(' . $option_specification->price . ')' ?>
        </option>
        <?php
        $compteur++;
    }
}