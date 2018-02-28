<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Cette page Web a pour but de définir les informations propres à la billeterie, à l'aide d'un formulaire. Une fois ce fait, il est possible d'éditer ces informations, et de lancer la billeterie.">

    <title> Définissez votre billeterie ! </title>

    <link rel="stylesheet" type="text/css" href="fonts/css/format.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"> </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="jumbotron text-center">
            <h1> Créez une billeterie </h1>
            <br>
            <h2> Définissez la billeterie que vous souhaitez utiliser, et présentez brièvement votre évènement ! </h2> <br>

            <p class="text-justify">
                Vous pouvez créer une billeterie sur PayIcam pour votre évènement. <br>
                La billeterie est prévue pour des évènements de taille relativement importante, évènements qui sont trop évolués pour être inclus dans le Shotgun. <br>
                Contactez nous pour bien remplir les informations et ne pas faire d'erreurs ! <br>
                <br>
                Vous pourrez toujours éditer les informations contenues dans le formualire après l'avoir créé. <br>
                Evitez par contre d'éditer les informations une fois la billeterie lancée sans maitriser ce que vous faites. <br>
            </p>
        </div>

        <br>

        <form method="post" action="ajout_billeterie.php">

            <div class="general_infos">

                <h3>I) Informations générales à propos de l'évènement même :</h3> <br>

                <div class="form-group">
                    <label for="event_name">Nom de votre évènement :</label>
                    <input type="text" class="form-control" name="event_name" id="event_name" placeholder="Nom de l'évènement" autofocus>
                </div>

                <div class="form-group">
                    <label for="event_description">Description de votre évènement :</label>
                    <textarea class="form-control" name="event_description" id="event_description" placeholder="Descrivez rapidement votre évènement" rows=3></textarea>
                </div>

                <div class="form-group">
                    <label for="event_quota">Quota de places disponibles pour votre évènement :</label>
                    <input type="number" min=0 class="form-control" name="event_description" id="event_quota" aria-describedby="quota_place_help" placeholder="Nombre de places" rows=3>
                    <small id="quota_place_help" class="form-text text-muted">Il ne sera pas possible de dépasser ce quota, les inscriptions se bloqueront automatiquement une fois ce nombre atteint.</small>
                </div>

                <div id="ticketing_dates">
                    <div class='form-group'>
                        <label for="ticketing_start_date">Début des inscriptions :</label>
                        <div class='input-group date' id='start_date_div'>
                            <input type='text' class="form-control" name="ticketing_start_date" id="ticketing_start_date" aria-describedby="ticketing_start_date_help" placeholder="Ouverture de la billeterie" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <small id="ticketing_start_date_help" class="form-text text-muted">A la date indiquée, la billeterie de votre évènement deviendra ouverte au public ciblé automatiquement.</small>
                    </div>
                    <div class='form-group'>
                        <label for="ticketing_end_date">Fin des inscriptions :</label>
                        <div class='input-group date' id='end_date_div'>
                            <input type='text' class="form-control" name="ticketing_end_date" id="ticketing_end_date" aria-describedby="ticketing_end_date_help" placeholder="Fermeture de la billeterie"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <small id="ticketing_end_date_help" class="form-text text-muted">A la date indiquée, la billeterie de votre évènement deviendra ouverte au public ciblé automatiquement.</small>
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

            </div>

            <br>

            <div class="availability">

                <h3 aria-describedby="availability_help">II) Accesibilité de votre évènement :</h3>
                <small id="availability_help">Répondez aux questions suivantes pour accéder à la suite du formulaire.</small> <br> <br>

                <div id="basic_availability">

                    <label>Autorisez vous l'accès de votre évènement aux promos diplomées ayant toujours un compte PayIcam?</label>
                    <div class="form-check">
                        <label class="radio-inline"><input type="radio" name="graduated_icam" value=1>Oui</label>
                        <label class="radio-inline"><input type="radio" name="graduated_icam" value=0>Non</label>
                    </div>
                    <br>
                    <label>Autorisez vous l'accès de votre évènement aux permanents?</label>
                    <div class="form-check">
                        <label class="radio-inline"><input type="radio" name="permanents" value=1>Oui</label>
                        <label class="radio-inline"><input type="radio" name="permanents" value=0>Non</label>
                    </div>
                    <br>
                    <label>Est-il possible d'inviter des personnes extérieures?</label>
                    <div class="form-check">
                        <label class="radio-inline"><input type="radio" name="guests" value=1>Oui</label>
                        <label class="radio-inline"><input type="radio" name="guests" value=0>Non</label>
                    </div>
                    <br>
                    <label>Proposez vous des options facultatives, gratuites ou payantes ?</label>
                    <div class="form-check">
                        <label class="radio-inline"><input type="radio" name="options" value=1>Oui</label>
                        <label class="radio-inline"><input type="radio" name="options" value=0>Non</label>
                    </div>
                    <br>

                    <div id="availability_complement">
                        <div id="table_availabilities">
                            <p>Ajoutez les promos qui doivent participer à qui vous ouvrez votre évènement ci dessous en indiquant le site et la promo !</p>

                            <div id="table_row_example">
                            <table>
                                <tr>
                                    <th>0</th>
                                    <td>Lille</td>
                                    <td>120</td>
                                    <td>0€</td>
                                    <td>500</td>
                                    <td>3</td>
                                    <td><button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></td>
                                </tr>
                            </table>
                            </div>

                            <table id="specification_table" class="table">
                                <thead>
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
                                <tbody>
                                </tbody>
                            </table>

                            <div id="specific_message"></div>
                        </div>

                        <div class="panel-group" id="accordion_accessibility_choice">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#site_specification"> Ajoutez par site</a>
                                    </h4>
                                </div>
                                <div id="site_specification" class="panel-collapse collapse panel-body">
                                    <label for="site_choice">Site:</label>
                                    <select multiple class="form-control" aria-describedby="site_choice_help" id="site_choice" size=4>
                                        <option disabled> Choisissez les sites qui participent à votre évènement </option>
                                        <option selected>Lille</option>
                                        <option>Toulouse</option>
                                        <option>Nantes</option>
                                    </select>
                                    <small id="site_choice_help" class="form-text text-muted"> Les sites renseignés ici auront accès à la billeterie de votre évènement. <br> PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>

                                    <br><br>

                                    <div id="site_price_quota_guests" class="row">
                                        <div id="site_only_price" class="col-sm-4 form-group">
                                            <label for="site_only_input_price">Prix de la sélection :</label>
                                            <input type="number" step="0.01" class="form-control" name="selection_price" id="site_only_input_price" aria-describedby="site_only_input_price_help" placeholder="Prix à fixer à la sélection" rows=3>
                                            <small id="site_only_input_price_help" class="form-text text-muted">Définissez le prix s'appliquant à votre sélection.</small>
                                        </div>

                                        <div id="site_only_quota" class="col-sm-4 form-group">
                                            <label for="site_only_input_quota">Quota pour chaque élément de la sélection :</label>
                                            <input type="number" class="form-control" name="selection_quota" id="site_only_input_quota" aria-describedby="site_only_input_quota_help" placeholder="Quota pour chaque élément" rows=3>
                                            <small id="site_only_input_quota_help" class="form-text text-muted">Définissez le quota pour chaque élément sélectionné</small>
                                        </div>

                                        <div id="site_only_guest_number" class="col-sm-4 form-group">
                                            <label for="site_only_input_guest_number">Nombre d'invités par étudiant par site</label>
                                            <input type="number" class="form-control" name="selection_guest_number" id="site_only_input_guest_number" aria-describedby="site_only_input_guest_number_help" placeholder="Nombre d'invités par étudiant" rows=3>
                                            <small id="site_only_input_guest_number_help" class="form-text text-muted">Définissez le nombre d'invités par étudiant.</small>
                                        </div>
                                    </div>

                                    <br>

                                    <button id="ajout_site" class="btn btn-success">Ajouter ce/ces sites d'étudiants</button>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#promo_specification"> Ajoutez par promo</a>
                                    </h4>
                                </div>
                                <div id="promo_specification" class="panel-collapse collapse panel-body">

                                    <div id="promo_complement" class="form-group">
                                        <label for="promo_choice">Promo:</label>
                                        <select multiple class="form-control" aria-describedby="promo_choice_help" id="promo_choice" size="11">
                                            <option disabled> Choisissez les promos qui participent à votre évènement (indépendamment de leur site) </option>
                                            <option>120</option>
                                            <option>119</option>
                                            <option>121</option>
                                            <option>122</option>
                                            <option>118</option>
                                            <option>2020</option>
                                            <option>2019</option>
                                            <option>2021</option>
                                            <option>2022</option>
                                            <option>2018</option>
                                        </select>
                                        <small id="promo_choice_help" class="form-text text-muted"> Les promos renseignées ici auront accès à la billeterie de votre évènement. Attention, ce sera valable pour tous les sites. <br>PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
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

                                    <button id="ajout_promo" class="btn btn-success">Ajouter ce/ces promos d'étudiants</button>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#site_and_promo_specification"> Ajoutez par promo et site</a>
                                    </h4>
                                </div>
                                <div id="site_and_promo_specification" class="panel-collapse collapse panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="site_and_promo_choice_site">Site:</label>
                                            <select multiple class="form-control" aria-describedby="site_and_promo_choice_help_site" id="site_and_promo_choice_site" height=60px size="4">
                                                <option disabled> Choisissez parmi les sites </option>
                                                <option>Lille</option>
                                                <option>Toulouse</option>
                                                <option>Nantes</option>
                                            </select>
                                            <small id="site_and_promo_choice_help_site" class="form-text text-muted"> Choisissez le ou les sites ...<br> PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label for="site_and_promo_choice_promo">Promo:</label>
                                            <select multiple class="form-control" aria-describedby="site_and_promo_choice_help_promo" id="site_and_promo_choice_promo" size="11">
                                                <option disabled> Choisissez parmi les promos</option>
                                                <option>120</option>
                                                <option>119</option>
                                                <option>121</option>
                                                <option>122</option>
                                                <option>118</option>
                                                <option>2020</option>
                                                <option>2019</option>
                                                <option>2021</option>
                                                <option>2022</option>
                                                <option>2018</option>
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

                                    <button id="ajout_site_and_promo" class="btn btn-success">Ajouter ces promos d'étudiants pour les sites sélectionnés</button>
                                </div>
                            </div>

                            <div id="graduated" class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion_accessibility_choice" href="#graduated_specification"> Ajoutez les diplomés par promo et site</a>
                                    </h4>
                                </div>
                                <div id="graduated_specification" class="panel-collapse collapse panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="graduated_choice_site">Site:</label>
                                            <select multiple class="form-control" aria-describedby="graduated_choice_help_site" id="graduated_choice_site" height=60px size="4">
                                                <option disabled> Choisissez parmi les sites </option>
                                                <option>Lille</option>
                                                <option>Toulouse</option>
                                                <option>Nantes</option>
                                            </select>
                                            <small id="graduated_choice_help_site" class="form-text text-muted"> Choisissez le ou les sites ...<br> PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label for="graduated_choice_promo">Promo:</label>
                                            <select multiple class="form-control" aria-describedby="graduated_choice_help_promo" id="graduated_choice_promo" size="4">
                                                <option disabled> Choisissez parmi les promos</option>
                                                <option>117</option>
                                                <option>116</option>
                                                <option>115</option>
                                            </select>
                                            <small id="graduated_choice_help_promo" class="form-text text-muted"> Et les promos correspondantes ! <br>PS: Utilisez Ctrl pour sélectionner plusieurs options, ou laissez le bouton de la souris appuyé, puis déplacez là.</small>
                                        </div>
                                    </div>

                                    <div id="graduated_price_quota_guests" class="row">
                                        <div id="graduated_only_price" class="col-sm-4 form-group">
                                            <label for="graduated_only_input_price">Prix de la sélection :</label>
                                            <input type="number" step="0.01" class="form-control" name="selection_price" id="graduated_only_input_price" aria-describedby="graduated_only_input_price_help" placeholder="Prix à fixer à la sélection" rows=3>
                                            <small id="graduated_only_input_price_help" class="form-text text-muted">Définissez le prix s'appliquant à votre sélection.</small>
                                        </div>

                                        <div id="graduated_only_quota" class="col-sm-4 form-group">
                                            <label for="graduated_only_input_quota">Quota pour chaque élément de la sélection :</label>
                                            <input type="number" class="form-control" name="selection_quota" id="graduated_only_input_quota" aria-describedby="graduated_only_input_quota_help" placeholder="Quota pour chaque élément" rows=3>
                                            <small id="graduated_only_input_quota_help" class="form-text text-muted">Définissez le quota pour chaque élément sélectionné</small>
                                        </div>

                                        <div id="graduated_only_guest_number" class="col-sm-4 form-group">
                                            <label for="graduated_only_input_guest_number">Nombre d'invités par étudiant par promo</label>
                                            <input type="number" class="form-control" name="selection_guest_number" id="graduated_only_input_guest_number" aria-describedby="graduated_only_input_guest_number_help" placeholder="Nombre d'invités par étudiant" rows=3>
                                            <small id="graduated_only_input_guest_number_help" class="form-text text-muted">Définissez le nombre d'invités par étudiant.</small>
                                        </div>
                                    </div>

                                    <br>

                                    <button id="ajout_graduated" class="btn btn-success">Ajouter ces promos de diplomés pour les sites sélectionnés</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="options">

                <h3 aria-describedby="option_help">III) Choix de vos options :</h3>
                <small id="option_help">Il est possible d'ajouter des options facultatives à votre évènement, payantes ou gratuites.</small> <br><br>

                <div class="panel-group" id="option_accordion">



                </div>
            </div>
        </form>
    </div>

    <script src="formulaire.js"></script>

</body>
</html>