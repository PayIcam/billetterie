<?php

//Possible qu'il y ait une erreur à cause de Php, qui a des problèmes avec la précision des float............
function is_an_integer($number)
{
    return (floor($number) == $number) && $number>=0;
}

function add_options_previously_defined($options)
{
    global $db;
    global $event_id;
    global $promos_specifications;

    $compteur=0;
    foreach($options as $option)
    {
        $compteur++;

        $promo_options = get_option_accessibility(array('event_id'=>$event_id, 'option_id'=>$option['option_id']));

        if(count($promo_options) == count($promos_specifications))
        {
            $all_opt = array("everyone_has_option" => 1);
        }
        else
        {
            $all_opt = array("everyone_has_option" => 0);
        }
        $option = array_merge($option, $all_opt);

        $option_specifications = json_decode($option['specifications']);
        add_option_html_code($compteur, $option, $option_specifications, $promo_options);
    }
}

function get_event_radio_values($promos_specifications)
{
    $guests = 0;
    $permanents = 0;
    $graduated = 0;

    global $list_graduated_promos;

    foreach($promos_specifications as $promo_specifications)
    {
        if(get_promo_name($promo_specifications['promo_id']) == 'Invités')
        {
            $guests = 1;
        }
        elseif(get_promo_name($promo_specifications['promo_id']) == 'Permanents')
        {
            $permanents = 1;
        }
        elseif(in_array(get_promo_name($promo_specifications['promo_id']), $list_graduated_promos))
        {
            $graduated = 1;
        }
    }
    return array("guests" => $guests, "permanents" => $permanents, "graduated" => $graduated);
}


function check_and_prepare_data()
{
    if(isset($_POST['event_data_json']) && $_POST['event_data_json']!='')
    {
        if($_POST['event_data_json']!='')
        {
            global $event;
            $event = json_decode($_POST['event_data_json']);
            if(!is_correct_event_data())
            {
                die();
            }
        }
        else
        {
            add_error("Les données de l'évènement sont vides");
            die();
        }
    }
    else
    {
        add_error("Les données de l'évènement n'ont pas été transmises.");
        die();
    }

    if(isset($_POST['event_accessibility_json']) && $_POST['event_accessibility_json']!='')
    {
        global $event_promos;
        $event_promos = json_decode($_POST['event_accessibility_json']);
        if(!is_correct_event_accessibility())
        {
            die();
        }
    }
    elseif($_POST['event_accessibility_json']=='')
    {
        add_error("Les données de l'accessibilité sont vides.");
        die();
    }
    else
    {
        add_error("Les données de l'accessibilité de l'évènement n'ont pas été transmises.");
        die();
    }

    if(isset($_POST['option_details_json']))
    {
        global $options;
        $options = json_decode($_POST['option_details_json'])==null ? array() : json_decode($_POST['option_details_json']);
        if(!are_correct_options())
        {
            die();
        }
    }
    else
    {
        add_error("Les données des options de l'évènement n'ont pas été transmises.");
        die();
    }
}


function is_correct_event_data()
{
    global $event;
    $error = false;

    if(!is_object($event))
    {
        add_error("Les informations sur l'évènement sont mal passées. Ce n'est même pas un objet.");
        return false;
    }
    if(count(get_object_vars($event))!=7)
    {
        add_error("Il n'y a pas le bon nombre de paramètres transmis pour les infos de l'évènement.");
        $error = true;
    }
    else
    {
        if(!is_string($event->name))
        {
            add_error("Le nom de l'évènement n'est même pas une chaine de caractères.");
            $error = true;
        }
        elseif(strlen($event->name)>60)
        {
            add_error("Est ce vraiment nécessaire d'avoir un nom d'évènement si grand ?");
            $error = true;
        }
        if(!is_string($event->description))
        {
            add_error("La description de l'évènement n'est même pas une chaine de caractères.");
            $error = true;
        }
        if(!is_numeric($event->quota))
        {
            add_error("Le quota de l'évènement n'est même pas numérique.");
            $error = true;
        }
        elseif(!is_an_integer($event->quota))
        {
            add_error("Le quota de l'évènement n'est même pas entier.");
            $error = true;
        }
        try
        {
            $event->ticketing_start_date = date('Y-m-d H:i:s', date_create_from_format('m/d/Y h:i a', $event->ticketing_start_date)->getTimestamp());
        }
        catch(Exception $exception)
        {
            add_error("Impossible de convertir la date de début de la billetterie.");
            $error = true;
        }
        try
        {
            $event->ticketing_end_date = date('Y-m-d H:i:s', date_create_from_format('m/d/Y h:i a', $event->ticketing_end_date)->getTimestamp());
        }
        catch(Exception $exception)
        {
            add_error("Impossible de convertir la date de fin de la billetterie.");
            $error = true;
        }
        if(!in_array($event->is_active, [0,1]))
        {
            add_error("Les données traitant de l'activité ou non de l'évènement ont mal été transmises.");
            $error = true;
        }
    }
    return !$error;
}

function is_correct_event_accessibility()
{
    global $event_promos;
    $error = false;

    foreach($event_promos as &$event_promo)
    {
        if(!is_object($event_promo))
        {
            add_error("Les informations sur une des options sont mal passées. Ce n'est même pas un objet.");
            $error = true;
            continue;
        }
        if(count(get_object_vars($event_promo))!=5)
        {
            add_error("Il n'y a pas le bon nombre de paramètres transmis pour l'accessibilité de l'évènement.");
            $error = true;
        }
        else
        {
            if(!is_string($event_promo->site))
            {
                add_error("Le nom du site n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $event_promo->site_id = get_site_id($event_promo->site);
                if(empty($event_promo->site_id))
                {
                    add_error("Aucun site ne correspond au site donné. (".$event_promo->site.")");
                    $error = true;
                }
            }
            if(!is_string($event_promo->promo))
            {
                add_error("Le nom du promo n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $event_promo->promo_id = get_promo_id($event_promo->promo);
                if(empty($event_promo->promo_id))
                {
                    add_error("Aucun promo ne correspond à la promo donné. (".$event_promo->promo.")");
                    $error = true;
                }
            }
            if(!is_numeric($event_promo->price))
            {
                add_error("Le prix d'une des promos n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(100*$event_promo->price))
            {
                add_error("Le prix d'une des promos est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                $error = true;
            }
            if(!is_numeric($event_promo->quota))
            {
                if(in_array($event_promo->quota, ['', null]))
                {
                    $event_promo->quota = null;
                }
                else
                {
                    add_error("Le quota d'une des promos n'est même pas numérique");
                    $error = true;
                }
            }
            elseif(!is_an_integer(1*$event_promo->quota))
            {
                add_error("Le quota d'une des promos n'est même pas entier");
                $error = true;
            }
            if(!is_numeric($event_promo->guest_number))
            {
                add_error("Le nombre d'invités d'une des promos n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(1*$event_promo->guest_number))
            {
                add_error("Le nombre d'invités d'une des promos n'est même pas entier");
                $error = true;
            }
        }
    }
    return !$error;
}

function are_correct_options()
{
    global $options;
    global $event;
    $error = false;

    foreach($options as &$option)
    {
        if(!is_object($option))
        {
            add_error("Les informations sur une des options sont mal passées. Ce n'est même pas un objet.");
            $error = true;
            continue;
        }
        if(isset($option->option_id))
        {
            if(!event_has_option(array("event_id" => $_GET['event_id'], "option_id" => $option->option_id)))
            {
                add_error("L'id de l'option a été changée.");
                $error = true;
                continue;
            }
        }
        if(!is_string($option->name))
        {
            add_error("Le nom de l'option n'est même pas une chaine de caractères");
            $error = true;
        }
        elseif(strlen($option->name)>45)
        {
            add_error("Est-il nécessaire d'avoir un nom si long pour votre option ?");
            $error = true;
        }
        elseif(strlen($event->name . " Option " . $option->name)>100)
        {
            add_error("Le nom combiné de votre évènement et de votre option est trop grand... Enlevez quelques caractères là ou vous pouvez. La description est faire pour ça !");
            $error = true;
        }
        if(!is_string($option->description))
        {
            add_error("La description de l'option n'est même pas une chaine de caractères");
            $error = true;
        }
        if($option->quota=='')
        {
            $option->quota=null;
        }
        elseif(!is_numeric($option->quota))
        {
            add_error("Le quota d'une des options n'est même pas numérique");
            $error = true;
        }
        elseif(!is_an_integer(1*$option->quota))
        {
            add_error("Le quota d'une des options n'est même pas entier");
            $error = true;
        }
        if(!in_array($option->is_active, [0,1]))
        {
            add_error("Les infos à propos de l'activation ou non de l'option sont mal passées.");
            $error = true;
        }
        if($option->type=='Checkbox')
        {
            $option->is_mandatory = 0;
            if(!is_numeric($option->type_specification->price))
            {
                add_error("Le prix d'une option checkbox n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(100*$option->type_specification->price))
            {
                add_error("Le prix d'une option checkbox est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                $error = true;
            }
        }
        elseif($option->type=='Select')
        {
            if(!in_array($option->is_mandatory, [0,1]))
            {
                add_error("Les infos à propos de la facultativité ou non de l'option sont mal passées.");
                $error = true;
            }
            if(count($option->type_specification)<=1)
            {
                add_error("Il n'y a qu'une seule option select, ce n'est pas normal. Autant utiliser une checkbox.");
                $error = true;
            }

            foreach($option->type_specification as &$suboption)
            {
                if(!is_object($suboption))
                {
                    add_error("Les informations sur une des sous-options select sont mal passées. Ce n'est même pas un objet.");
                    $error = true;
                    continue;
                }
                if(!is_string($suboption->name))
                {
                    add_error("Le nom d'une sous-option select n'est même pas une chaine de caractères");
                    $error = true;
                }
                elseif(!strlen($suboption->name)>40)
                {
                    add_error("Est-il nécessaire d'avoir une sous-option si longue ?");
                    $error = true;
                }
                elseif(strlen($event->name . " Option " . $option->name . " Choix " . $suboption->name)>100)
                {
                    add_error("Le nom combiné de votre évènement, de votre option, et de votre sous-option est trop grand... Enlevez quelques caractères là ou vous pouvez.");
                    $error = true;
                }
                if(!is_numeric($suboption->price))
                {
                    add_error("Le prix d'une sous-option select n'est même pas numérique");
                    $error = true;
                }
                elseif(!is_an_integer(100*$suboption->price))
                {
                    add_error("Le prix d'une sous-option select est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                    $error = true;
                }
                if(!is_numeric($suboption->quota))
                {
                    if(in_array($suboption->quota, [null, '']))
                    {
                        $suboption->quota = null;
                    }
                    else
                    {
                        add_error("Le quota d'une sous-option select n'est même pas numérique");
                        $error = true;
                    }
                }
                elseif(!is_an_integer(1*$suboption->quota))
                {
                    add_error("Le quota d'une sous-option select n'est pas un entier");
                    $error = true;
                }
            }
        }
        else
        {
            add_error("Le type de l'option n'est pas bien défini.");
            $error = true;
        }
        if(count($option->accessibility)==0)
        {
            add_error("Aucune promo n'a le droit à cette option.");
            $error = true;
        }
        foreach($option->accessibility as &$promo)
        {
            if(!is_string($promo->site))
            {
                add_error("Le nom du site n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $promo->site_id = get_site_id($promo->site);
                if(empty($promo->site_id))
                {
                    add_error("Aucun site ne correspond au site donné. (".$promo->site.")");
                    $error = true;
                }
            }
            if(!is_string($promo->promo))
            {
                add_error("Le nom du promo n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $promo->promo_id = get_promo_id($promo->promo);
                if(empty($promo->promo_id))
                {
                    add_error("Aucun promo ne correspond à la promo donné. (".$promo->promo.")");
                    $error = true;
                }
            }
        }
    }
    return !$error;
}