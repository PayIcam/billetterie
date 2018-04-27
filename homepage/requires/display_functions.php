<?php

/**
 * On affiche les évènements, avec leur titre & description, ainsi que le lien vers la billetterie. Selon le ticketing_state, l'affichage sera légèrement différent
 * @param  string $ticketing_state
 * @param  array $event                [fetch de events]
 * @param  boolean $icam_has_reservation [true si l'Icam a une réservation]
 */
function display_event($ticketing_state, $event, $icam_has_reservation)
{
    global $_CONFIG;
    switch ($ticketing_state)
    {
        case 'open':
        {
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-success">En cours</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=htmlspecialchars($event['description'])?></p>
                    <a href="<?=$_CONFIG['public_url']?>inscriptions/<?=$icam_has_reservation ? "edit_reservation.php" : "inscriptions.php" ?>?event_id=<?=$event['event_id']?>" class="btn btn-primary col-sm-3">
                        <?=$icam_has_reservation ? "Modifier sa réservation" : "S'inscrire" ?>
                    </a>
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
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-info">Ouvre bientôt</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=htmlspecialchars($event['description'])?></p>
                    <button class="btn btn-primary col-sm-3" disabled>Inscrivez vous</button><!-- On ne met pas le lien ici, ça n'a aucun intérêt -->
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
                <h2 class="text-center"><?=htmlspecialchars($event['name'])?> <span class="label label-info">Terminé</span></h2>
                <br>
                <div class="row">
                    <p class="col-sm-9" style="font-size: 1.5em;">Description : <?=htmlspecialchars($event['description'])?></p>
                    <a href="<?=$_CONFIG['public_url']?>inscriptions/edit_reservation.php?event_id=<?=$event['event_id']?>" class="btn btn-primary col-sm-3">Regarder sa réservation</a>
                </div>
            </div>
            <hr>
            <?php
            break;
        }
    }
}