<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Cette page Web a pour but de permettre aux utilisateurs de PayIcam de s'inscrire à un évènement.">

        <title>Billetterie PayIcam</title>

        <link rel="stylesheet" href="../css/format.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    </head>
    <body>

    <div class="container" style="width: 70%; margin-top:0.5%">
        <div class="jumbotron">
            <h1 class="text-center">Billetterie PayIcam</h1><br><hr>
            <h2>Inscrivez vous aux évènements qui vous intéressent ! </h2>
        </div>
    </div>
    <div class="container" id="billetterie" style="width: 50%">
        <?php handle_ticketings_displayed($events_id_accessible); ?>
    </div>

    </body>
</html>