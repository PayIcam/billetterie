<?php

function insert_select_options($option_specifications, $is_mandatory = 0)
{
    $compteur=0;
    foreach($option_specifications as $option_specification)
    {
        ?>
        <option value="<?= $option_specification->name ?>" <?=($is_mandatory==1 and $compteur==0) ? 'selected' : ''?> >
            <?= $option_specification->name . '(' . $option_specification->price . '€)' ?>
        </option>
        <?php
        $compteur++;
    }
}

function add_error($message)
{
    ?>
    <div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Attention ! </strong> <?=$message?>
    </div>
    <?php
}

function set_alert_style()
{
    ?>
    <link rel="stylesheet" href="../../css/format.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <style>
        .alert
        {
            margin: 2%;
        }
    </style>
    <?php
}