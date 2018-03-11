<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Cette page Web a pour but de permettre aux utilisateurs de PayIcam de s'inscrire à un évènement.">

    <title>Inscriptions : <?= $event['name'] ?></title>

    <link rel="stylesheet" href="../fonts/css/format.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
</head>
<body>
    <div id="presentation" class="container">
        <div class="jumbotron">
            <h1 class="text-center"><?= $event['name'] ?></h1>
            <h2><?= $event['description'] ?></h2>
            <h3>Inscrivez vous en remplissant le formulaire ci dessous, et en validant ! Pensez à recharger afin d'avoir de quoi payer au préalable ! </h3>
        </div>
    </div>
    <form method="post" action="php/ajout_participant.php?event_id=<?=$event_id?>">
        <div id="registration">
            <div id="registration_icam" class="container">
                <?php
                $promo_quota = $promo_specifications['quota']=='' ? INF : $promo_specifications['quota'];
                if(get_current_promo_quota(array('event_id' => $event_id, 'promo_id' => $promo_id, 'site_id' => $site_id)) < $promo_quota)
                {
                    form_icam($event, $promo_specifications, $options);
                }
                else
                {
                    echo 'Toutes les places proposées à votre promo ont été vendues...';
                }
                ?>
            </div>
            <div id="registration_guests" class="container row">
                <?php
                if($promo_specifications['guest_number']>0)
                {
                    $guests_specifications = get_promo_specification_details(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

                    $temporary_guest_number = ($total_quota-$current_participants_number > $promo_specifications['guest_number'] ) ? $promo_specifications['guest_number'] : $total_quota-$current_participants_number;
                    $temporary_guest_number = $temporary_guest_number>=0 ? $temporary_guest_number : 0;

                    if($temporary_guest_number<$promo_specifications['guest_number'])
                    {
                        echo "Il n'y a pas assez de places encore disponibles pour tout l'évènement pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_id). ".<br>";
                    }

                    $guest_quota = $guests_specifications['quota'];
                    $current_guests_number = get_current_promo_quota(array('event_id' => $event_id, 'promo_id' => get_promo_id('Invités'), 'site_id' => $site_id));

                    $actual_guest_number = ($guest_quota-$current_guests_number > $temporary_guest_number) ? $temporary_guest_number : $guest_quota-$current_guests_number;

                    $actual_guest_number = $actual_guest_number>=0 ? $actual_guest_number : 0;

                    if($actual_guest_number<$temporary_guest_number)
                    {
                        echo "Il n'y a pas assez de places encore disponibles pour les invités pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_id). ".<br>";
                    }

                    for($i=1; $i<=$actual_guest_number; $i++)
                    {
                        form_guest($event, $guests_specifications, $options, $i);
                    }
                }
                ?>
            </div>
            <div id="hidden_inputs">
                <input type="hidden" name="icam_informations">
                <input type="hidden" name="guests_informations">
            </div>
        </div>
        <div id="errors"></div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Passer au payement</button>
        </div>
    </form>
    <script src="jquery/submit_inscriptions.js"></script>
    <script src="jquery/general_behaviour.js"></script>
    <script src="jquery/inscriptions.js"></script>
</body>
</html>