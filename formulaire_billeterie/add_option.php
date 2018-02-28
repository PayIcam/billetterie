<?php

function add_option($option_number)
{
    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a data-toggle="collapse" data-parent="#option_accordion" href=<?= '"#option_'.$option_number.'"'; ?> >Option sans nom</a>
                            </div>
                        </div>

                        <div id=<?= '"option_'.$option_number.'"'; ?> class="panel-collapse collapse panel-body">
                            <div class="option_generalities">
                                <h4>A) Généralités sur l'option</h4>
                                <div class="form-group">
                                    <label for="option_name">Nom de votre option :</label>
                                    <input type="text" class="form-control" name="option_name" id="option_name" placeholder="Nom de l'option" autofocus>
                                </div>

                                <div class="form-group">
                                    <label for="option_description">Description de votre option :</label>
                                    <textarea class="form-control" name="option_description" id="option_description" placeholder="Descrivez rapidement votre option" rows=3></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="option_description">Quota de places disponibles pour votre option :</label>
                                    <input type="number" min=0 class="form-control" name="option_description" id="option_description" aria-describedby="quota_option_help" placeholder="Nombre de places" rows=3>
                                    <small id="quota_option_help" class="form-text text-muted">Il ne sera pas possible de dépasser ce quota, cette option se bloquera automatiquement une fois ce nombre atteint.</small>
                                </div>

                                <label>Quel est le type de l'option que vous proposez ?</label>
                                <div class="form-check" aria-describedby="option_accessibility_help">
                                    <label class="radio-inline"><input type="radio" name="option_type" value="Checkbox">Checkbox</label>
                                    <label class="radio-inline"><input type="radio" name="option_type" value="Select">Select</label>
                                </div>
                                <small id="option_accessibility_help">
                                    <br>
                                    Une checkbox ne permet qu'un seul choix, simple, alors qu'un select, plus précis, permet un choix parmi une liste de propositions. <br>
                                    Une checkbox est donc adaptée à une question de type : "Venez vous le matin ?"<br>
                                    Un select est par contre adaptée à une question de type : "Venez vous le matin, le midi, ou le soir ?"<br>
                                    <br>
                                    Pour l'exemple nous vous décrivons les deux types en questions (non stylisés).<br>Pas d'inquiétudes, cocher ces exemples ou non est sans effet !<br>
                                    Ceci est une checkbox : Matin : <input type="checkbox"> <br>
                                    Ceci est un select : <select><option>Matin</option><option>Midi</option><option>Soir</option></select> <br> <br>
                                </small>

                                <label>Votre option est-t-elle accessible à tous les participants définis au dessus ?</label>
                                <div class="form-check" aria-describedby="option_accessibility_help">
                                    <label class="radio-inline"><input type="radio" name="everyone_has_option" value=1>Oui</label>
                                    <label class="radio-inline"><input type="radio" name="everyone_has_option" value=0>Non</label>
                                </div>
                                <small id="option_accessibility_help">Il est possible de restreindre l'accès à une option si vous cochez la case "Oui".</small>

                                <br><br>

                            </div>

                            <div class="option_type_complement">

                                <h4>B) Décrivez votre option (prix, possibilités) </h4>

                                <div class="checkbox_type">
                                    <h5>Option de type checkbox :</h5>
                                    <div class="form-group option_checkbox_price">
                                        <label for="option_checkbox_price_input">Prix de l'option:</label>
                                        <input type="number" step="0.01" class="form-control" name="checkbox_price" id="option_checkbox_price_input" aria-describedby="option_checkbox_price_input_help" placeholder="Prix à fixer à la sélection" rows=3>
                                        <small id="option_checkbox_price_input_help" class="form-text text-muted">Définissez le prix de votre option checkbox.</small> <br>
                                    </div>
                                    <div class="checkbox_example form-check">
                                        <span>Voici un exemple de l'affichage de votre checkbox dans le formulaire à remplir pour s'inscrire à l'évènement !</span> <br>
                                        <input id="checkbox_example" class="form-check-input" type="checkbox">
                                        <label class="form-check-label" for="checkbox_example"></label>
                                    </div>
                                </div>
                                <div class="select_type">
                                    <h5>Option de type select :</h5>
                                    <table class="specification_table table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Prix</th>
                                                <th scope="col">Supprimer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="form-group col-sm-6 option_select_name">
                                            <label for="option_select_name_input">Nom de la sous-option:</label>
                                            <input type="text" class="form-control" name="select_name" id="option_select_name_input" aria-describedby="option_select_name_input_help" placeholder="Nom la sous-option">
                                            <small id="option_select_name_input_help" class="form-text text-muted">Définissez le nom de votre sous-option.</small> <br>
                                        </div>

                                        <div class="form-group col-sm-6 option_select_price">
                                            <label for="option_select_price_input">Prix de la sous-option:</label>
                                            <input type="number" step="0.01" class="form-control" name="select_price" id="option_select_price_input" aria-describedby="option_select_price_input_help" placeholder="Prix à fixer à la sélection">
                                            <small id="option_select_price_input_help" class="form-text text-muted">Définissez le prix de votre sous-option.</small> <br>
                                        </div>
                                    </div>

                                    <button class="btn btn-success ajout_select">Ajouter ce/ces sous-options</button> <br> <br>

                                    <div class="select_example form-check">
                                        <span>Voici un exemple de l'affichage de votre select dans le formulaire à remplir pour s'inscrire à l'évènement !</span> <br>
                                        <label class="form-check-label" for="select_example"></label>
                                        <select class="form-control select_select_example">
                                            <option disabled>Sélectionnez votre option !</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="option_accessibility">

                                <h4>C) Enlevez les promos qui ne doivent pas avoir accès à votre option </h4>

                                <table class="table option_accessibility_table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Site</th>
                                            <th scope="col">Promo</th>
                                            <th scope="col">Supprimer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <button class="btn btn-info option_accessibility_restart">Réinitialiser les promos ayant accès à l'option</button>

                            </div>
                        </div>
                    </div>
    <?php
}

if(isset($_GET['option_number']))
{
    add_option($_GET['option_number']);
}
else
{
    echo 'Mdr ça a pas marché';
}
