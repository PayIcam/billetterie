<?php

function set_header_navbar($title)
{
    global $_CONFIG, $is_super_admin, $event_id, $Auth;
    ?>
    <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="description" content="Cette page Web a pour but de permettre aux utilisateurs de PayIcam de s'inscrire à un évènement.">

                <title><?=htmlspecialchars($title)?></title>

                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
                <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
                <link rel="stylesheet" type="text/css" href="<?=$_CONFIG['public_url']?>css/format.css">

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"> </script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
                <style>
                    body
                    {
                        padding-top: 55px;
                    }
                </style>
                <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111260636-2"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());

                    gtag('config', 'UA-111260636-2');
                </script>
                <script>
                    $(document).ready(function()
                    {
                        $('[data-toggle="popover"]').popover();
                        public_url = '<?=$_CONFIG['public_url']?>';
                        base_path = '<?=$_CONFIG['base_path']?>';
                        event_id = '<?=$event_id ?? ""?>';
                    });
                </script>

            </head>
            <body>
                <nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <li><a href="https://payicam.icam.fr/accueil-payicam/">Accueil PayIcam</a></li>
                            <li><a href="<?=$_CONFIG['public_url']?>">Accueil Billetterie</a></li>
                            <?php
                            echo $Auth->hasRole('member') ? '<li><a href="' . $_CONFIG['public_url'] . 'event_administration">Administration des évènements</a></li>' : '';

                            echo $Auth->hasRole('member++') ? '<li><a href="' . $_CONFIG['public_url'] . 'participant_administration">Administration des participants</a></li>' : '';

                            echo $is_super_admin ? '<li><a href="https://payicam.icam.fr/scoobydoo">Scoobydoo</a></li>' : ''?>
                        </ul>
                    </div>
                </nav>
    <?php
}

function add_alert($message, $class="danger")
{
    ?>
    <div class="alert alert-<?=$class?> alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Attention ! </strong> <?=$message?>
    </div>
    <?php
}

function add_alert_to_ajax_response($message, $class="danger")
{
    ob_start(); ?>
    <div class="alert alert-<?=$class?> alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Attention ! </strong> <?=$message?>
    </div>
    <?php $html_code_error = ob_get_clean();

    global $ajax_json_response;
    $ajax_json_response['message'] .= $html_code_error;
}

function set_alert_style($title)
{
    global $_CONFIG, $is_super_admin, $event_id;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?=$title?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../../css/format.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111260636-2"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-111260636-2');
        </script>

        <style>
            body
            {
                padding-top: 55px;
            }
            .alert
            {
                margin: 2%;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li><a href="https://payicam.icam.fr/accueil-payicam/">Accueil PayIcam</a></li>
                    <li><a href="<?=$_CONFIG['public_url']?>">Accueil Billetterie</a></li>
                    <li><a href="<?=$_CONFIG['public_url']?>event_administration">Administration Billetterie</a></li>
                    <?=$is_super_admin ? '<li><a href="https://payicam.icam.fr/scoobydoo">Scoobydoo</a></li>' : ''?>
                </ul>
            </div>
        </nav>
    <?php
}

function insert_as_select_option($array_to_insert)
{
    foreach ($array_to_insert as $element)
    {
        echo '<option>'. htmlspecialchars($element) .'</option>';
    }
}

/**
 * unused
 */
function display_pending_reservations_entrees($participant)
{
    if($participant['is_icam']==1)
    {
        if(count(get_pending_reservations($participant['event_id'], $participant['email'])) >=1)
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
}