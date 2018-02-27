<?php

function diner_conference($participant)
{
    if ($participant['repas'] ==1 or $participant['buffet']==1)
    {
        if ($participant['repas'] ==1 and $participant['buffet'] ==1)
        {
        echo htmlspecialchars('Dîner et conférence');
        }
        elseif ($participant['repas'] ==1 and !$participant['buffet'] ==1)
        {
        echo htmlspecialchars('Dîner');
        }
        else
        {
        echo htmlspecialchars('Conférence');
        }
    }
    else
    {
        echo htmlspecialchars('Pas d\'options');
    }
}
function ajustement_creneau($creneau, $echo=true)
{
    switch ($creneau)
    {
        case '21h-21h45':
            $creneau = '<span style="color: blue">21h-21h35 </span> ';
            break;
        case '21h45-22h30':
            $creneau = '<span style="color: red">21h50-22h25 </span> ';
            break;
        case '22h30-23h':
            $creneau = '<span style="color: green">22h40-23h10 </span> ';
            break;
    }
    if($echo==true)
    {
        echo ($creneau);
    }
    return $creneau;
}
function four_chars_bracelet_id($bracelet_id)
{
    $id = strval($bracelet_id);
    $len = strlen($id);
    switch($len)
    {
        case 1:
        {
            $zeros ='000';
            $id = $zeros.$id;
            break;
        }
        case 2:
        {
            $zeros ='00';
            $id = $zeros.$id;
            break;
        }
        case 3:
        {
            $zeros ='0';
            $id = $zeros.$id;
            break;
        }
    }
    switch((int)$id)
    {
        case 0:
            $id= '<span style="color: orange">'.$id.'</span>';
            break;
        case (int)$id <=1050:
        {
            $id = '<span style="color: blue">'.$id.'</span>';
            break;
        }
        case (int)$id<=1900:
        {
            $id = '<span style="color: green">'.$id.'</span>';
            break;
        }
        case (int)$id<=2850:
        {
            $id = '<span style="color: red">'.$id.'</span>';
            break;
        }
        case (int)$id<=3200:
        {
            $id = '<span style="color: orange">'.$id.'</span>';
            break;
        }
    }
    return $id;
}
function is_correct_bracelet($bracelet_id, $creneau, $id_exempted)
{
    switch ($bracelet_id)
    {
        case "":
        {
            $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('."caractère vide". ') incorrecte ! Recommencez svp';
            return false;
        }
        case $bracelet_id<=1050:
        {
            if ($creneau != '21h-21h45' and $creneau != 'BAR 117')
            {
                $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('.four_chars_bracelet_id($bracelet_id).') de bracelet de 1er créneau ! Recommencez svp';
                return false;
            }
            else
            {
                $infos_bracelets_pris = get_liste_bracelets('21h-21h45', $id_exempted);
                foreach($infos_bracelets_pris as $info_bracelet_pris)
                {
                    if (in_array($bracelet_id, $info_bracelet_pris))
                    {
                        $prenom = $info_bracelet_pris['prenom'];
                        $nom= $info_bracelet_pris['nom'];
                        $_SESSION['erreur_bracelet'] ='Le bracelet '.four_chars_bracelet_id($bracelet_id).' est déjà pris par '.$prenom.' '.$nom.'! Recommencez svp';
                        return false;
                    }
                }
                return true;
            }
        }
        case $bracelet_id<=1900:
        {
            if ($creneau != '22h30-23h')
            {
                $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('.four_chars_bracelet_id($bracelet_id).') de bracelet de 3e créneau ! Recommencez svp';
                return false;
            }
            else
            {
                $infos_bracelets_pris = get_liste_bracelets('22h30-23h', $id_exempted);
                foreach($infos_bracelets_pris as $info_bracelet_pris)
                {
                    if (in_array($bracelet_id, $info_bracelet_pris))
                    {
                        $prenom = $info_bracelet_pris['prenom'];
                        $nom= $info_bracelet_pris['nom'];
                        $_SESSION['erreur_bracelet'] ='Le bracelet '.four_chars_bracelet_id($bracelet_id).' est déjà pris par '.$prenom.' '.$nom.'! Recommencez svp';
                        return false;
                    }
                }
                return true;
            }
        }
        case $bracelet_id<=2850:
        {
            if ($creneau != '21h45-22h30')
            {
                $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('.four_chars_bracelet_id($bracelet_id).') de bracelet de 2e créneau ! Recommencez svp';
                return false;
            }
            else
            {
                $infos_bracelets_pris = get_liste_bracelets('21h45-22h30', $id_exempted);
                foreach($infos_bracelets_pris as $info_bracelet_pris)
                {
                    if (in_array($bracelet_id, $info_bracelet_pris))
                    {
                        $prenom = $info_bracelet_pris['prenom'];
                        $nom= $info_bracelet_pris['nom'];
                        $_SESSION['erreur_bracelet'] ='Le bracelet '.four_chars_bracelet_id($bracelet_id).' est déjà pris par '.$prenom.' '.$nom.'! Recommencez svp';
                        return false;
                    }
                }
                return true;
            }
        }
        case $bracelet_id<=3200:
        {
            $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('.four_chars_bracelet_id($bracelet_id).') de bracelet orange (spécial)! Recommencez svp';
            return false;
        }
        default:
        {
            $_SESSION['erreur_bracelet'] ='Vous avez entré une id ('.four_chars_bracelet_id($bracelet_id).') incorrecte ! Recommencez svp';
            return false;
        }
    }
}
function color_percentage($percentage)
{
    switch ($percentage)
    {
        case 'undefined':
        {
            break;
        }
        case $percentage<25:
        {
            $percentage = '<span style="color:#cc0000; font-weight:bold">'.$percentage.'%</span>';
            break;
        }
        case $percentage<50:
        {
            $percentage = '<span style="color:#ff6600">'.$percentage.'%</span>';
            break;
        }
        case $percentage<75:
        {
            $percentage = '<span style="color:#ffcc00">'.$percentage.'%</span>';
            break;
        }
        case $percentage<90:
        {
            $percentage = '<span style="color:blue">'.$percentage.'%</span>';
            break;
        }
        case $percentage<100:
        {
            $percentage = '<span style="color:#50D050">'.$percentage.'%</span>';
            break;
        }
        case 100:
        {
            $percentage = '<span style="color:green; font-weight:bold">'.$percentage.'%</span>';
            break;
        }
        default:
        {
        }
    }
    return $percentage;
}
function adjust_hour_data($hour_data, $hour_groups)
{
    $first_day = $hour_data[0]['day'];
    foreach($hour_data as $hour)
    {
        if($hour['day'] == $first_day)
        {
            $day1[] = array();
        }
        else
        {
            $day2[] = array();
        }
    }
}