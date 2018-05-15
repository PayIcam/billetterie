<?php

function is_correct_config_edition()
{
    $error = false;
    if(isset($_POST['ticketing']))
    {
        if(!in_array($_POST['ticketing'], [0,1]))
        {
            $error=true;
        }
    }
    else
    {
        $error = true;
        add_alert("Les informations à propos de l'activité de la billetterie ne sont pas passées.");
    }
    if(isset($_POST['event_administration']))
    {
        if(!in_array($_POST['event_administration'], [0,1]))
        {
            $error=true;
        }
    }
    else
    {
        $error = true;
        add_alert("Les informations à propos de l'activité de l'administration des évènements ne sont pas passées.");
    }
    if(isset($_POST['inscriptions']))
    {
        if(!in_array($_POST['inscriptions'], [0,1]))
        {
            $error=true;
        }
    }
    else
    {
        $error = true;
        add_alert("Les informations à propos de l'activité des inscriptions ne sont pas passées.");
    }
    if(isset($_POST['participant_administration']))
    {
        if(!in_array($_POST['participant_administration'], [0,1]))
        {
            $error=true;
        }
    }
    else
    {
        $error = true;
        add_alert("Les informations à propos de l'activité de l'administration des participants ne sont pas passées.");
    }
    return !$error;
}