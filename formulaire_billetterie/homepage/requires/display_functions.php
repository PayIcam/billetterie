<?php

function display_event($ticketing_state, $event, $icam_has_reservation)
{
    switch ($ticketing_state)
    {
        case 'open':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=$event['name']?> <span class="label label-success">En cours</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <form method="post" style="display: inline" action="inscriptions/<?=$icam_has_reservation ? "edit_reservation.php" : "inscriptions.php" ?>?event_id=<?=$event['event_id']?>">
                        <button class="btn btn-primary col-sm-3">
                                <?=$icam_has_reservation ? "Editer votre réservation" : "Inscrivez vous" ?>
                        </button>
                    </form>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
        case 'coming soon':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=$event['name']?> <span class="label label-info">Ouvre bientôt</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <button class="btn btn-primary col-sm-3" disabled>Inscrivez vous</button>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
        case 'ended not long ago and reservation':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=$event['name']?> <span class="label label-info">Terminé</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=$event['description']?></p>
                    <form method="post" style="display: inline" action="inscriptions/edit_reservation.php?event_id=<?=$event['event_id']?>">
                        <button class="btn btn-primary col-sm-3">Regarder sa réservation</button>
                    </form>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
    }
}