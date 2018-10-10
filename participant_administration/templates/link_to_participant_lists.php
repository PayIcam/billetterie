<!-- Template de l'index de l'administration des participants, avec des liens basiques vers la liste des participants -->

<?php set_header_navbar("Administration")?>
        <div class="container jumbotron" style="width: 70%; margin-top:0.5%">
            <h1 class="text-center">Administration des participants</h1><br><hr>
            <h1 class="text-center">Regardez qui a réservé sa place pour votre évènement, administrez les, regardez des statistiques !</h1>
        </div>

        <div class="container">
            <div class="row">
                <?php
                $i=1;
                foreach($fundations as $fundation)
                {
                    if($fundation->name == 'Toutes les fundations') {
                        $i-=1;
                        continue;
                    }
                    $i = display_fundations_participants_admin($fundation, $i);
                    if($i%3==0)
                    {
                        echo '</div><div class="row">';
                    }
                }
                ?>
            </div>
        </div>
    </body>
</html>