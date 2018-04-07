<?php set_header_navbar("Ajout d'options à un participant")?>

        <h1 class="text-center">Ajouter des options à <?=isset($icam) ? htmlspecialchars($icam['prenom']) . ' ' . htmlspecialchars($icam['nom']) : '' . '(' . htmlspecialchars($event['name']) . ')' ?></h1><hr><br>

        <?php isset($icam) ? $icam['is_icam']==1 ? one_row_participant_table($icam, 'info_icam') : one_row_participant_table($icam, 'info_invite') : "" ?>

        <form method="POST" action="php/ajout_options.php?event_id=<?=isset($icam) ? $event_id.'&participant_id='.$icam['participant_id'] : $event_id?>">
            <div class="container">
            <hr>
                <div id="options">
                    <h2 class="text-center">Choisissez les options que vous offrez</h2>
                    <div class="row option">
                        <?php
                        foreach($options as $option)
                        {
                            $option['specifications'] = json_decode($option['specifications']);
                            display_option_no_checking($option);
                        }
                        ?>
                    </div>
                </div>
                <hr>
                <div id="message_submit" class="container">
                    <div class="alert alert-info alert-dismissible waiting">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Parfait !</strong> Modification en cours
                    </div>
                    <br><br>
                </div>
                <div id="alerts"></div>
                <div class="text-center">
                    <button id="button_submit_form" class="btn btn-primary" type="submit">Ajouter</button>
                </div>
            </div>
        </form>
        <script src="jquery/ajout_options.js"></script>
    </body>
</html>












