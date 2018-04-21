<?php

function display_search_possibilities()
{
    ?>
    - Prénom <br>
    - Nom <br>
    - Prénom + espace + nom <br>
    - Promo exacte <br>
    - Site exact <br>
    - Identifiant de bracelet <br>
    <?php
}

function display_participants_rows($participants)
{
    foreach($participants as $participant)
    {
        $participant = prepare_participant_displaying($participant);
        $participant['site'] = get_site_name($participant['site_id']);
        $participant['is_in'] = participant_has_arrived($participant['participant_id']);
        ?>
        <tr data-participant_id=<?=$participant['participant_id']?>>
            <td><span class='badge badge-pill badge-success'><?=$participant['bracelet_identification']?></span></td>
            <td><?=$participant['prenom']?></td>
            <td><?=$participant['nom']?></td>
            <td><span class='badge badge-pill badge-info'><?=get_promo_name($participant['promo_id'])?></span></td>
            <?=display_options($participant)?>
            <?=display_guest_infos($participant)?>
            <?=display_personnal_informations($participant)?>
            <?=display_validate_button($participant)?>
        </tr>
        <?php
    }
}