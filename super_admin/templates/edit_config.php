<?php set_header_navbar("Ajout d'options à un participant"); ?>
        <h1 class="text-center">Editer la configuration de l'évènement</h1><br>

        <div class="container">
            <form action="php/edit_config.php" method="POST">
                <?php
                foreach($config as $folder)
                {
                    display_folder_activity_edition($folder);
                }

                ?>
                <div id="message_submit" class="container">
                    <div class="alert alert-info alert-dismissible waiting">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Parfait !</strong> Modification en cours
                    </div>
                    <br><br>
                </div>
                    <div class="text-center">
                    <button id="button_submit_form" class="btn btn-primary" type="submit">Valider</button>
                </div>
            </form>
        </div>
        <div id="alerts"></div>
        <script src="jquery/edit_config.js"></script>
    </body>
</html>