<?php

/////////////////////////
//   Page principale   //
/////////////////////////

session_start();

// si aucun mot de passe ou login est détecté on ne charge pas la page
if(!isset($_POST["login"]) OR !isset($_POST["motdepasse"])){
    exit();
}

// sinon on les récupère
$login = $_POST["login"];
$motdepasse = $_POST["motdepasse"];

// on vérifie si la combinaison login/mot de passe est correcte
if($login != "gala" || $motdepasse != "gala"){
    exit();
}
?>

<!DOCTYPE html>
<html html lang='fr'>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/style.css" />
        <title>Entrées Gala 2018</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    </head>

    <body>
        <div class = 'container'>
            <div class="row">
                <div class="col">
                    <h3>Entrées du Gala des Icam 2018</h3>
                </div>
            </div>
            <form method="post">
                <div class="row">
                    <div class= "col-md-3">
                        <!--  zone de recherche -->
                        <input type="input-medium search-query" onKeyUp="chercher(this.value);" class="form-control" id="recherche" placeholder="Nom, prenom..."
                        value="">
                    </div>
                    <div class="col-md-3">
                    <p id="nb_invites">affichage de 0/0 résultats</p>
                </div>
                </div>
            </form>
            <br>
            <section class="row">
                <!-- Tableau pour l'affichage des résultats -->
                <table class="table table-sm table-striped table-bordered" id="tableau">
                </table>
            </section>
        </div>

        <p id="nb_entrees">0/0 entrées</p>
        
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

        <script type="text/javascript" src="js/oXHR.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </body>
</html>