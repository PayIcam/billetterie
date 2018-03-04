<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Cette page Web a pour but de définir les informations propres à la billetterie, à l'aide d'un formulaire. Une fois ce fait, il est possible d'éditer ces informations, et de lancer la billetterie.">

    <title> Définissez votre billetterie ! </title>

    <link rel="stylesheet" type="text/css" href="fonts/css/format.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"> </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="jumbotron text-center">
            <h1> Créez une billetterie </h1>
            <br>
            <h2> Définissez la billetterie que vous souhaitez utiliser, et présentez brièvement votre évènement ! </h2> <br>

            <p class="text-justify">
                Vous pouvez créer une billetterie sur PayIcam pour votre évènement. <br>
                La billetterie est prévue pour des évènements de taille relativement importante, évènements qui sont trop évolués pour être inclus dans le Shotgun. <br>
                Contactez nous pour bien remplir les informations et ne pas faire d'erreurs ! <br>
                <br>
                Vous pourrez toujours éditer les informations contenues dans le formualire après l'avoir créé. <br>
                Evitez par contre d'éditer les informations une fois la billetterie lancée sans maitriser ce que vous faites. <br>
            </p>
        </div>

        <br>

        <form method="post" action="php/<?= isset($event) ? 'edit_billetterie.php?event_id='.$event_id : 'ajout_billetterie.php' ?>">

            <div class="general_infos">

                <h3>I) Informations générales à propos de l'évènement même :</h3> <br>

                <div class="form-group">
                    <label for="event_name">Nom de votre évènement :</label>
                    <input value="<?= $event['name'] ?? '' ?>" type="text" class="form-control" name="event_name" id="event_name" placeholder="Nom de l'évènement" autofocus required>
                </div>

                <div class="form-group">
                    <label for="event_description">Description de votre évènement :</label>
                    <textarea class="form-control" name="event_description" id="event_description" placeholder="Descrivez rapidement votre évènement" required><?= $event['description'] ?? '' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="event_quota">Quota de places disponibles pour votre évènement :</label>
                    <input value="<?= $event['total_quota'] ?? '' ?>" type="number" min=0 class="form-control" name="event_quota" id="event_quota" aria-describedby="quota_place_help" placeholder="Nombre de places" required>
                    <small id="quota_place_help" class="form-text text-muted">Il ne sera pas possible de dépasser ce quota, les inscriptions se bloqueront automatiquement une fois ce nombre atteint.</small>
                </div>

                <div id="ticketing_dates">
                    <div class='form-group'>
                        <label for="ticketing_start_date">Début des inscriptions :</label>
                        <div class='input-group date' id='start_date_div'>
                            <input value="<?= $event['ticketing_start_date'] ?? '' ?>" type='text' class="form-control" name="ticketing_start_date" id="ticketing_start_date" aria-describedby="ticketing_start_date_help" placeholder="Ouverture de la billetterie" required>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <small id="ticketing_start_date_help" class="form-text text-muted">A la date indiquée, la billetterie de votre évènement deviendra ouverte au public ciblé automatiquement.</small>
                    </div>
                    <div class='form-group'>
                        <label for="ticketing_end_date">Fin des inscriptions :</label>
                        <div class='input-group date' id='end_date_div'>
                            <input value="<?= $event['ticketing_end_date'] ?? '' ?>" type='text' class="form-control" name="ticketing_end_date" id="ticketing_end_date" aria-describedby="ticketing_end_date_help" placeholder="Fermeture de la billetterie" required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <small id="ticketing_end_date_help" class="form-text text-muted">A la date indiquée, la billetterie de votre évènement deviendra ouverte au public ciblé automatiquement.</small>
                    </div>
                </div>
                <script type="text/javascript">
                /**
                 * Small script mostly taken from Bootstrap.
                 *
                 * It denies the user the possibility of setting the ticketing start date anterior to the ticketing end date.
                 *
                 * What I added ensured that when you clicked on the input itself, it would trigger the click on the gliphicon, thus opening
                 * the date choice.
                 */
                    $(function () {
                        $('#ticketing_dates input').click(function()
                        {
                            $(this).next('span').click();
                        });

                        $('#start_date_div').datetimepicker({
                            sideBySide: true
                        });
                        $('#end_date_div').datetimepicker({
                            sideBySide: true,
                            useCurrent: false //Important! See issue #1075
                        });
                        $("#start_date_div").on("dp.change", function (e) {
                            $('#end_date_div').data("DateTimePicker").minDate(e.date);
                        });
                        $("#end_date_div").on("dp.change", function (e) {
                            $('#start_date_div').data("DateTimePicker").maxDate(e.date);
                        });
                    });
                </script>

                <div class="form-group">
                    <input id="event_is_active" name="event_is_active" type="checkbox" data-toggle="toggle" data-on="Activer" data-off="Désactiver" aria-describedby="event_is_active_help" value=1>
                    <label for="event_is_active">Activer votre billetterie dès maintenant ?</label><br>
                    <small id="event_is_active_help">Vous pouvez laisser ce bouton décoché le temps de préparer votre billetterie, et le rendre actif au moment venu. Attention, ce bouton est prioritaire sur la date d'activation de votre billetterie. Pour qu'il soit possible de s'inscrire, il faut être entre les deux dates ET que ce bouton soit coché.</small>
                    <?php if(isset($event['is_active']))
                    {
                        if ($event['is_active']==1)
                        {
                            ?> <script> $("#event_is_active").click(); </script> <?php
                        }
                    }?>
                </div>

            </div>
            <br>

            <div class="availability"><!--Par accessibilité, entendre, qui peux s'inscrire. C'est tout le sens de cette partie-->

                <h3 aria-describedby="availability_help">II) Accesibilité de votre évènement :</h3>
                <small id="availability_help">Répondez aux questions suivantes pour accéder à la suite du formulaire.</small> <br> <br>

                <div id="basic_availability">

                    <label>Autorisez vous l'accès de votre évènement aux promos diplomées ayant toujours un compte PayIcam?</label>
                    <div class="form-check"><!--Si on dit oui, on pourra choisir parmi une liste de promos de diplômés ayant accès à PI-->
                        <label class="radio-inline"><input <?= isset($event_radios['graduated']) ? ($event_radios['graduated']==1 ? 'checked' : '') : '' ?> type="radio" name="graduated_icam" value=1 required>Oui</label>
                        <label class="radio-inline"><input <?= isset($event_radios['graduated']) ? ($event_radios['graduated']==0 ? 'checked' : '') : '' ?> type="radio" name="graduated_icam" value=0>Non</label>
                    </div>
                    <br>
                    <label>Autorisez vous l'accès de votre évènement aux permanents?</label>
                    <div class="form-check"><!--Si on dit oui, on accepte tous les permanents, la ligne apparait dans le tableau ci dessous, plus qu'à edit-->
                        <label class="radio-inline"><input <?= isset($event_radios['permanents']) ? ($event_radios['permanents']==1 ? 'checked' : '') : '' ?> type="radio" name="permanents" value=1 required>Oui</label>
                        <label class="radio-inline"><input <?= isset($event_radios['permanents']) ? ($event_radios['permanents']==0 ? 'checked' : '') : '' ?> type="radio" name="permanents" value=0>Non</label>
                    </div>
                    <br>
                    <label>Est-il possible d'inviter des personnes extérieures?</label>
                    <div class="form-check"><!--Si on clique sur oui, on peux définir le nombre d'invités par promo, sinon, il vaut 0-->
                        <label class="radio-inline"><input <?= isset($event_radios['guests']) ? ($event_radios['guests']==1 ? 'checked' : '') : '' ?> type="radio" name="guests" value=1 required>Oui</label>
                        <label class="radio-inline"><input <?= isset($event_radios['guests']) ? ($event_radios['guests']==0 ? 'checked' : '') : '' ?> type="radio" name="guests" value=0>Non</label>
                    </div><!--ça ajoute aussi une promo invités pour définir leur prix et quotas (ils peuvent pas avoir d'invités par contre mdr)-->
                    <br>
                    <label>Proposez vous des options facultatives, gratuites ou payantes ?</label>
                    <div class="form-check"><!--Si on clique sur Oui, on affiche en dynamique les options en bas-->
                        <label class="radio-inline"><input <?= isset($event_radios['options']) ? ($event_radios['options']==1 ? 'checked' : '') : '' ?> type="radio" name="options" value=1 required>Oui</label>
                        <label class="radio-inline"><input <?= isset($event_radios['options']) ? ($event_radios['options']==0 ? 'checked' : '') : '' ?> type="radio" name="options" value=0>Non</label>
                    </div>
                    <br>

                    <div id="availability_complement">
                        <div id="table_availabilities">
                            <p>Ajoutez les promos qui doivent participer à qui vous ouvrez votre évènement ci dessous en indiquant le site et la promo !</p>

                            <div id="table_row_example">
                            <table><!--A l'époque, j'étais nul en Jquery donc je savais que cloner, c'est vrai que c'est du foutage de gueule mais bon-->
                                <tr><!--L'idée est de cloner une structure de base, et changer toutes les valeurs-->
                                    <th>0</th>
                                    <td>Lille</td>
                                    <td>120</td>
                                    <td>0€</td>
                                    <td>500</td>
                                    <td>3</td>
                                    <td><button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
                                </tr>
                            </table>
                            </div>

                            <table id="specification_table" class="table">
                                <thead><!--On définit quand même le format de la table-->
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Site</th>
                                        <th scope="col">Promo</th>
                                        <th scope="col">Prix</th>
                                        <th scope="col">Quota</th>
                                        <th scope="col">Nombre d'invités</th>
                                        <th scope="col">Supprimer</th>
                                    </tr>
                                </thead>
                                <tbody><!--Le tbody va se remplir dynamiquement grâce à Jquery, pas d'inquiétudes-->
                                    <?php isset($promos_specifications) ? insert_event_accessibility_rows($promos_specifications) : '' ?>
                                </tbody>
                            </table>

                            <div id="specific_message"></div><!--On affiche des tooltips, ou des messages d'erreurs ici.-->
                        </div>

                        <div class="panel-group" id="accordion_accessibility_choice"><!--Les sites, on va pouvoir ajouter pour toutes les promos d'un site-->

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#site_specification"> Ajoutez par site</a>
                                    </h4>
                                </div>
                                <div id="site_specification" class="panel-collapse collapse panel-body">
                                    <label for="site_choice">Site:</label>
                                    <select multiple class="form-control" aria-describedby="site_choice_help" id="site_choice">
                                        <option disabled> Choisissez les sites qui participent à votre évènement </option>
                                        <?php insert_as_select_option($sites); ?><!--On récupère la liste des sites grâce à un petit bout de php-->
                                    </select>
                                    <small id="site_choice_help" class="form-text text-muted"> Les sites renseignés ici auront accès à la billetterie de votre évènement. <br> PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>

                                    <br><br>

                                    <div id="site_price_quota_guests" class="row">
                                        <div id="site_only_price" class="col-sm-4 form-group"><!--On définit le prix pour tout le site-->
                                            <label for="site_only_input_price">Prix de la sélection :</label>
                                            <input type="number" step="0.01" class="form-control" name="selection_price" id="site_only_input_price" aria-describedby="site_only_input_price_help" placeholder="Prix à fixer à la sélection" rows=3>
                                            <small id="site_only_input_price_help" class="form-text text-muted">Définissez le prix s'appliquant à votre sélection.</small>
                                        </div>

                                        <div id="site_only_quota" class="col-sm-4 form-group"><!--On définit le quota pour chaque promo du site (pas tt le site)-->
                                            <label for="site_only_input_quota">Quota pour chaque élément de la sélection :</label>
                                            <input type="number" class="form-control" name="selection_quota" id="site_only_input_quota" aria-describedby="site_only_input_quota_help" placeholder="Quota pour chaque élément" rows=3>
                                            <small id="site_only_input_quota_help" class="form-text text-muted">Définissez le quota pour chaque élément sélectionné</small>
                                        </div>

                                        <div id="site_only_guest_number" class="col-sm-4 form-group"><!--Et le nombre d'invités par promo du site-->
                                            <label for="site_only_input_guest_number">Nombre d'invités par étudiant par site</label>
                                            <input type="number" class="form-control" name="selection_guest_number" id="site_only_input_guest_number" aria-describedby="site_only_input_guest_number_help" placeholder="Nombre d'invités par étudiant" rows=3>
                                            <small id="site_only_input_guest_number_help" class="form-text text-muted">Définissez le nombre d'invités par étudiant.</small>
                                        </div>
                                    </div>

                                    <br>

                                    <button type="button" id="ajout_site" class="btn btn-success">Ajouter ce/ces sites d'étudiants</button>
                                </div>
                            </div>

                            <div class="panel panel-default"><!--Les promos, la même chose que les sites, mais réarrangé-->
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#promo_specification"> Ajoutez par promo</a>
                                    </h4>
                                </div>
                                <div id="promo_specification" class="panel-collapse collapse panel-body">

                                    <div id="promo_complement" class="form-group">
                                        <label for="promo_choice">Promo:</label>
                                        <select multiple class="form-control" aria-describedby="promo_choice_help" id="promo_choice">
                                            <option disabled> Choisissez les promos qui participent à votre évènement (indépendamment de leur site) </option>
                                            <?php insert_as_select_option($student_promos); ?>
                                        </select>
                                        <small id="promo_choice_help" class="form-text text-muted"> Les promos renseignées ici auront accès à la billetterie de votre évènement. Attention, ce sera valable pour tous les sites. <br>PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                    </div>


                                    <div id="promo_price_quota_guests" class="row">
                                        <div id="promo_only_price" class="col-sm-4 form-group">
                                            <label for="promo_only_input_price">Prix de la sélection :</label>
                                            <input type="number" step="0.01" class="form-control" name="selection_price" id="promo_only_input_price" aria-describedby="promo_only_input_price_help" placeholder="Prix à fixer à la sélection" rows=3>
                                            <small id="promo_only_input_price_help" class="form-text text-muted">Définissez le prix s'appliquant à votre sélection.</small>
                                        </div>

                                        <div id="promo_only_quota" class="col-sm-4 form-group">
                                            <label for="promo_only_input_quota">Quota pour chaque élément de la sélection :</label>
                                            <input type="number" class="form-control" name="selection_quota" id="promo_only_input_quota" aria-describedby="promo_only_input_quota_help" placeholder="Quota pour chaque élément" rows=3>
                                            <small id="promo_only_input_quota_help" class="form-text text-muted">Définissez le quota pour chaque élément sélectionné</small>
                                        </div>

                                        <div id="promo_only_guest_number" class="col-sm-4 form-group">
                                            <label for="promo_only_input_guest_number">Nombre d'invités par étudiant par promo</label>
                                            <input type="number" class="form-control" name="selection_guest_number" id="promo_only_input_guest_number" aria-describedby="promo_only_input_guest_number_help" placeholder="Nombre d'invités par étudiant" rows=3>
                                            <small id="promo_only_input_guest_number_help" class="form-text text-muted">Définissez le nombre d'invités par étudiant.</small>
                                        </div>
                                    </div>

                                    <br>

                                    <button type="button" id="ajout_promo" class="btn btn-success">Ajouter ce/ces promos d'étudiants</button>
                                </div>
                            </div>

                            <div class="panel panel-default"><!--Les sites & promos, une combinaison des deux du haut -->
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#site_and_promo_specification"> Ajoutez par promo et site</a>
                                    </h4>
                                </div>
                                <div id="site_and_promo_specification" class="panel-collapse collapse panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="site_and_promo_choice_site">Site:</label>
                                            <select multiple class="form-control" aria-describedby="site_and_promo_choice_help_site" id="site_and_promo_choice_site" height=60px>
                                                <option disabled> Choisissez parmi les sites </option>
                                                <?php insert_as_select_option($sites); ?>
                                            </select>
                                            <small id="site_and_promo_choice_help_site" class="form-text text-muted"> Choisissez le ou les sites ...<br> PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label for="site_and_promo_choice_promo">Promo:</label>
                                            <select multiple class="form-control" aria-describedby="site_and_promo_choice_help_promo" id="site_and_promo_choice_promo">
                                                <option disabled> Choisissez parmi les promos</option>
                                                <?php insert_as_select_option($student_promos); ?>
                                            </select>
                                            <small id="site_and_promo_choice_help_promo" class="form-text text-muted"> Et les promos correspondantes ! <br>PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                        </div>
                                    </div>

                                    <div id="site_and_promo_price_quota_guests" class="row">
                                        <div id="site_and_promo_only_price" class="col-sm-4 form-group">
                                            <label for="site_and_promo_only_input_price">Prix de la sélection :</label>
                                            <input type="number" step="0.01" class="form-control" name="selection_price" id="site_and_promo_only_input_price" aria-describedby="site_and_promo_only_input_price_help" placeholder="Prix à fixer à la sélection" rows=3>
                                            <small id="site_and_promo_only_input_price_help" class="form-text text-muted">Définissez le prix s'appliquant à votre sélection.</small>
                                        </div>

                                        <div id="site_and_promo_only_quota" class="col-sm-4 form-group">
                                            <label for="site_and_promo_only_input_quota">Quota pour chaque élément de la sélection :</label>
                                            <input type="number" class="form-control" name="selection_quota" id="site_and_promo_only_input_quota" aria-describedby="site_and_promo_only_input_quota_help" placeholder="Quota pour chaque élément" rows=3>
                                            <small id="site_and_promo_only_input_quota_help" class="form-text text-muted">Définissez le quota pour chaque élément sélectionné</small>
                                        </div>

                                        <div id="site_and_promo_only_guest_number" class="col-sm-4 form-group">
                                            <label for="site_and_promo_only_input_guest_number">Nombre d'invités par étudiant par promo</label>
                                            <input type="number" class="form-control" name="selection_guest_number" id="site_and_promo_only_input_guest_number" aria-describedby="site_and_promo_only_input_guest_number_help" placeholder="Nombre d'invités par étudiant" rows=3>
                                            <small id="site_and_promo_only_input_guest_number_help" class="form-text text-muted">Définissez le nombre d'invités par étudiant.</small>
                                        </div>
                                    </div>

                                    <br>

                                    <button type="button" id="ajout_site_and_promo" class="btn btn-success">Ajouter ces promos d'étudiants pour les sites sélectionnés</button>
                                </div>
                            </div>

                            <div id="graduated" class="panel panel-default"><!-- Vestige de l'ancien code, regardez les premiers sur Commit si vous êtes curieux -->
                                <div id="graduated_promos_infos">
                                    <select id="graduated_promos_select">
                                    <?php insert_as_select_option($graduated_promos); ?>
                                    </select>
                                </div><!--Ce select sert quand même être cloné, et ajouté ailleurs-->
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="options">

                <h3 aria-describedby="option_help">III) Choix de vos options :</h3>
                <small id="option_help">Il est possible d'ajouter des options facultatives à votre évènement, payantes ou gratuites.</small> <br><br>

                <button type="button" class="btn btn-success" id="add_option_button"><span class="glyphicon glyphicon-plus" title="Ajoutez une option"></span></button><!--Petit bouton sympa permettant d'ajouter une option à la main.-->

                <div class="panel-group" id="option_accordion">
                    <?php isset($options) ? add_options_previously_defined($options) : '' ?>
                </div><!--On va avoir TOUTES nos options ici, on les ajoute avec un peu d'AJAX-->
            </div>

            <div id="submit_form_div" class="text-center"><!--Basique, c'est notre bouton de submit, il ne s'affiche pas dès le début d'ailleurs cf JS -->
                <button id="submit_form" class="btn btn-success" type="submit"><?= isset($event) ? 'Editez ' : 'Créez ' ?>votre évènement !</button>
            </div>

            <div id="erreurs_submit"></div><!--On va mettre ici des erreurs qui s'affichent si on a mal submit -->

            <div id="input_additions">
            </div><!--On va mettre ici des inputs hidden juste avant d'envoyer les données. Ils contiennent des données grâce au JSON.stringify(). -->
        </form>
    </div>

    <script src="jquery/submit.js"></script>
    <script src="jquery/event_functions.js"></script>
    <script src="jquery/option_functions.js"></script>
    <script src="jquery/formulaire.js"></script>
    <script src="jquery/edit.js"></script>
    <script>
        $(document).ready(function()
        {
            <?php
            if(isset($event))
            {?>
                edit_no_options_action();
                <?php
                if(isset($options))
                {
                    ?>
                    attach_option_events();
                    edit_options_action();
                    <?php
                }
            }?>
        });
    </script>
</body>
</html>