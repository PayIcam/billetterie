<?php

function display_event($ticketing_state, $event)
{
    switch ($ticketing_state)
    {
        case 'open':
            ?>
            <div class="event_open">
                <h2 class="text-center"><?=$event['name']?></h2>
                <p style="font-size: 1.5em;">Description : <?=$event['description']?></p>
            </div>
            <?php
            break;
        case 'waiting':

            break;
    }
}