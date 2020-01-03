<!-- Template des entrées d'un évènement. L'immense partie se fait en Ajax, c'est pour cela qu'on trouve aussi peu d'éléments ici -->

<?php set_header_navbar($title) ?>

        <div class="container">
            <h1 class="text-center"><?=$title?></h1>
            <?php display_back_to_list_button($event_id); ?>
            <h2><span id="nombre_entrees"><?=$arrival_number?></span> / <?=$participants_number?> Entrées</h2>

            <h2>
                Recherchez les participants !
                <a data-toggle="collapse" href="#search_help" role="button" aria-expanded="false" aria-controls="search_help">
                    <span class="glyphicon glyphicon-menu-down"></span>
                </a>
            </h2>

            <div class="collapse" id="search_help">
                <div class="card card-body">
                    <h3>Particularités de la recherche</h3>
                    <p>
                        La recherche est dynamique, pas besoin de valider ! <br>
                        Il n'est pas nécessaire attention aux accents, ni au majuscules non plus ! <br>
                        La recherche se lance quand au moins 2 caractères sont rentrés, sauf si ce sont des nombres <br>
                        Possibilités de recherche :
                        <button class="btn option_tooltip" data-container="body" data-toggle="popover" data-html="true" title="Possibilités de recherche" data-content="<?=display_search_possibilities()?>" type="button">
                            <span class="glyphicon glyphicon-question-sign option_tooltip_glyph"></span>
                        </button>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="recherche" name="recherche" placeholder="Entrez votre recherche" autocomplete="off">
                    <span id="badgeuse_indicator" class="input-group-addon" title="Connexion au lecteur de carte : non établie"><span class="glyphicon glyphicon-hdd"></span> <span class="badge badge-pill badge-warning" id="on_off">OFF</span></span>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="col-sm-1" scope="col">Bracelet</th>
                        <th class="col-sm-1" scope="col">Prénom</th>
                        <th class="col-sm-1" scope="col">Nom</th>
                        <th class="col-sm-1" scope="col">Promo</th>
                        <th class="col-sm-1" scope="col">Options</th>
                        <th class="col-sm-1" scope="col">Invités</th>
                        <th class="col-sm-1" scope="col">Informations</th>
                        <th class="col-sm-1" scope="col">Valider</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div id="alerts"></div>
        <script src="jquery/entrees.js"></script>
        <script src="jquery/carte_lecteur.js"></script>
    </body>
</html>