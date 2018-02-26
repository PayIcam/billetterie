<?php

/**
 *
 * Ici, on rappelle les informations de la personne qu'on Edit
 *
 * Il y a deux cas : soit la personne sur laquelle on edit est un Icam soit elle ne l'est pas.
 *
 * Cela se reflète donc directement dans le code.
 *
 */

// ___________ ICAM ___________
if ($participant_sous_edit['is_icam'] ==1)
{
    tableau_une_ligne($participant_sous_edit, 'info_icam');
}
// ___________ Invité ___________
else
{
    tableau_une_ligne($participant_sous_edit, 'info_invite');
}