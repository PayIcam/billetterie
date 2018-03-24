<?php set_header_navbar("Billetterie PayIcam")?>
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