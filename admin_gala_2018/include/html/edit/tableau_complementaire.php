<?php

/**
 *
 * Ici, on affiche les informations sur les liens invité/inviteur.
 *
 * Il y a deux cas : soit la personne sur laquelle on edit est un Icam soit elle ne l'est pas.
 *
 * Cela se reflète donc directement dans le code.
 *
 */
// ___________ ICAM => affiche les invités ___________
if ($participant_sous_edit['is_icam'] ==1)
{
    if ($nombre_invites_et_total[0] >0)
    { ?>
    <p class="titre_invite"> Ses invités </p>

    <div class="container">
        <section class="row" id="tableau">
            <table class="table table-striped">
                <thead>
                    <?php display_liste_head('link_invite') ?>
                </thead>
                <tbody>
                <?php
                foreach($invites_rattaches as $invite_rattache)
                {
                    display_participant_info($invite_rattache, 'link_invite');
                }
                ?>
                </tbody>
            </table>
        </section>
    </div>
    <?php
    }
}
// ___________ Invité => affiche l'Icam qui invite ___________
else
{
    ?>
    <p class="titre_invite"> L'Icam qui a invité </p>

    <?php tableau_une_ligne($participant_inviteur, 'link_icam');
}