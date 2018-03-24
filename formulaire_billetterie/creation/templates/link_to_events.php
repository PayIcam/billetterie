<?php set_header_navbar("Administration")?>
        <div class="container jumbotron" style="width: 70%; margin-top:0.5%">
            <h1 class="text-center">Administration des billetteries</h1><br><hr>
            <h1 class="text-center">Créez votre billetterie, ou éditez là !</h1>
        </div>

        <div class="container">
            <div class="row">
                <?php
                foreach($fundations as $fundation)
                {
                    if($fundation->name == 'Toutes les fundations')
                        continue;
                    display_fundations_events_admin($fundation);
                }
                ?>
            </div>
        </div>
    </body>
</html>