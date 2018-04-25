<?php

function set_header_navbar($title)
{
    global $_CONFIG, $is_super_admin, $event_id;
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
                            <li><a href="<?=$_CONFIG['public_url']?>creation">Administration Billetterie</a></li>
                            <?=$is_super_admin ? '<li><a href="https://payicam.icam.fr/scoobydoo">Scoobydoo</a></li>' : ''?>
                        </ul>
                    </div>
                </nav>
    <?php
}

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
                    <li><a href="<?=$_CONFIG['public_url']?>creation">Administration Billetterie</a></li>
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

function display_guest_infos($participant)
{
    global $event_id;
    ?> <td> <?php
        if($participant['is_icam'] == 1)
        {
            $guests = get_icams_guests(array('event_id' => $_GET['event_id'], 'icam_id' => $participant['participant_id']));
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

function create_guests_text($guests)
{
    foreach($guests as $guest)
    {
        echo $guest['prenom'] . ' ' . $guest['nom'] . '<br>';
    }
}

function create_personal_informations_text($participant)
{
    ?>
    <strong>Site :</strong> <span class='badge badge-pill badge-inverse'><?=$participant['site']?></span> <br>
    <strong>Prix :</strong> <span class='badge badge-pill badge-info'><?=get_participant_option_prices($participant['participant_id']) + $participant['price']?>€</span> <br>
    <strong>Payement :</strong> <span class='badge badge-pill badge-success'><?=$participant['payement']?></span> <br>
    <?= isset($participant['telephone']) ? "<strong>Telephone :</strong> <span class='badge badge-pill badge-warning'>" . $participant['telephone'] . "</span><br>" : "" ?>
    <strong>Inscription :</strong> <span class='badge badge-pill badge-error'><?=date('d/m/Y à H:i:s', date_create_from_format('Y-m-d H:i:s', $participant['inscription_date'])->getTimestamp())?></span> <br>
    <?= isset($participant['email']) ? "<strong>Email :</strong> <span class='badge badge-pill badge-inverse'>" . $participant['email'] . "</span><br>" : "" ?>
    <?php
}

function display_personnal_informations($participant)
{
    ?>
    <td>
        <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Informations supplémentaires" data-content="<?= create_personal_informations_text($participant) ?>" type="button">
            <span class="glyphicon glyphicon-eye-open option_tooltip_glyph"></span>
        </button>
    </td>
    <?php
}

function display_validate_button($participant)
{
    ?>
    <td>
        <?= $participant['is_in'] ?
        '<button class="is_in option_tooltip btn btn-danger" data-container="body" type="button">✘</button>' :
        '<button class="is_out option_tooltip btn btn-success" data-container="body" type="button">✔</button>' ?>
    </td>
    <?php
}

function create_option_text($option_choices)
{
    foreach($option_choices as $option_choice)
    {
        $option_message = $option_choice['name']==null ? "" : " Choix " . $option_choice['name'];
        echo get_option_name($option_choice['option_id']) . $option_message. '<br>';
    }
}

function display_back_to_list_button($event_id)
{
    global $_CONFIG;
    ?>
    <div class="container">
        <a class="btn btn-primary" href="<?=$_CONFIG['public_url']?>stats/participants.php?event_id=<?=$event_id?>">Retour à la liste</a>
    </div>
    <?php
}
function display_go_to_arrivals($event_id)
{
    global $_CONFIG;
    ?>
    <div class="container">
        <a class="btn btn-primary" href="<?=$_CONFIG['public_url']?>entrees/entrees.php?event_id=<?=$event_id?>">Aller aux entrées</a>
    </div>
    <?php
}