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

function get_ticketing_state($event, $promo_id, $site_id, $email, $icam_has_reservation)
{
    $event_id = $event['event_id'];

    date_default_timezone_set('Europe/Paris');
    $current_datetime = new DateTime();
    $ticketing_start_date = new DateTime($event['ticketing_start_date']);
    $ticketing_end_date = new DateTime($event['ticketing_end_date']);

    $ticketing_state = 'open';
    if($current_datetime < $ticketing_start_date)
    {
        $interval = $current_datetime->diff($ticketing_start_date);
        if($interval->y > 0 || $interval->m > 0 || $interval->d > 10)
        {
            $ticketing_state = 'coming in some time';
        }
        $ticketing_state = 'coming soon';
    }
    elseif($current_datetime > $ticketing_end_date)
    {
        $interval = $current_datetime->diff($ticketing_end_date);
        if($interval->y > 0 || $interval->m > 0 || $interval->d > 10)
        {
            $ticketing_state = 'ended long ago';
        }
        elseif(!$icam_has_reservation)
        {
            $ticketing_state = 'ended and no reservation';
        }
        $ticketing_state = 'ended not long ago and reservation';
    }
    return $ticketing_state;
}