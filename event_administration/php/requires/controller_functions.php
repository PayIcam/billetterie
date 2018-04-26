<?php

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

        $option_choices = get_option_choices($option['option_id']);
        add_option_html_code($compteur, $option, $option_choices, $promo_options);
    }
}

function get_event_radio_values($promos_specifications)
{
    $guests = 0;
    $permanents = 0;
    $graduated = 0;

    global $graduated_promos;

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
        elseif(in_array(get_promo_name($promo_specifications['promo_id']), $graduated_promos))
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
            add_alert("Les données de l'évènement sont vides");
            die();
        }
    }
    else
    {
        add_alert("Les données de l'évènement n'ont pas été transmises.");
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
        add_alert("Les données de l'accessibilité sont vides.");
        die();
    }
    else
    {
        add_alert("Les données de l'accessibilité de l'évènement n'ont pas été transmises.");
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
        add_alert("Les données des options de l'évènement n'ont pas été transmises.");
        die();
    }
}


function is_correct_event_data()
{
    global $event;
    $error = false;

    if(!is_object($event))
    {
        add_alert("Les informations sur l'évènement sont mal passées. Ce n'est même pas un objet.");
        return false;
    }
    if(count(get_object_vars($event))!=7)
    {
        add_alert("Il n'y a pas le bon nombre de paramètres transmis pour les infos de l'évènement.");
        $error = true;
    }
    else
    {
        if(!is_string($event->name))
        {
            add_alert("Le nom de l'évènement n'est même pas une chaine de caractères.");
            $error = true;
        }
        elseif(strlen($event->name)>60)
        {
            add_alert("Est ce vraiment nécessaire d'avoir un nom d'évènement si grand ?");
            $error = true;
        }
        if(!is_string($event->description))
        {
            add_alert("La description de l'évènement n'est même pas une chaine de caractères.");
            $error = true;
        }
        if(!is_numeric($event->quota))
        {
            add_alert("Le quota de l'évènement n'est même pas numérique.");
            $error = true;
        }
        elseif(!is_an_integer($event->quota))
        {
            add_alert("Le quota de l'évènement n'est même pas entier.");
            $error = true;
        }
        try
        {
            $event->ticketing_start_date = date('Y-m-d H:i:s', date_create_from_format('m/d/Y h:i a', $event->ticketing_start_date)->getTimestamp());
        }
        catch(Exception $exception)
        {
            add_alert("Impossible de convertir la date de début de la billetterie.");
            $error = true;
        }
        try
        {
            $event->ticketing_end_date = date('Y-m-d H:i:s', date_create_from_format('m/d/Y h:i a', $event->ticketing_end_date)->getTimestamp());
        }
        catch(Exception $exception)
        {
            add_alert("Impossible de convertir la date de fin de la billetterie.");
            $error = true;
        }
        if(!in_array($event->is_active, [0,1]))
        {
            add_alert("Les données traitant de l'activité ou non de l'évènement ont mal été transmises.");
            $error = true;
        }
        if(isset($event->fundation_id))
        {
            if(!is_numeric($event->fundation_id))
            {
                add_alert("L'id de la fondation n'est pas numérique.");
                $error = true;
            }
            elseif(!is_an_integer($event->fundation_id))
            {
                add_alert("L'id de la fondation n'est pas entière.");
                $error = true;
            }
            else
            {
                check_user_fundations_rights($event->fundation_id, false);
            }
        }
        else
        {
            add_alert("L'id de la fondation sur laquelle ajouter l'évènement n'est pas transmis.");
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
            add_alert("Les informations sur une des options sont mal passées. Ce n'est même pas un objet.");
            $error = true;
            continue;
        }
        if(count(get_object_vars($event_promo))!=5)
        {
            add_alert("Il n'y a pas le bon nombre de paramètres transmis pour l'accessibilité de l'évènement.");
            $error = true;
        }
        else
        {
            if(!is_string($event_promo->site))
            {
                add_alert("Le nom du site n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $event_promo->site_id = get_site_id($event_promo->site);
                if(empty($event_promo->site_id))
                {
                    add_alert("Aucun site ne correspond au site donné. (".$event_promo->site.")");
                    $error = true;
                }
            }
            if(!is_string($event_promo->promo))
            {
                add_alert("Le nom du promo n'est même pas une chaine de caractères.");
                $error = true;
            }
            else
            {
                $event_promo->promo_id = get_promo_id($event_promo->promo);
                if(empty($event_promo->promo_id))
                {
                    add_alert("Aucun promo ne correspond à la promo donné. (".$event_promo->promo.")");
                    $error = true;
                }
            }
            if(!is_numeric($event_promo->price))
            {
                add_alert("Le prix d'une des promos n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(100*$event_promo->price))
            {
                add_alert("Le prix d'une des promos est défini avec une précision plus grande que le centime, ou n'est même pas positif");
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
                    add_alert("Le quota d'une des promos n'est même pas numérique");
                    $error = true;
                }
            }
            elseif(!is_an_integer(1*$event_promo->quota))
            {
                add_alert("Le quota d'une des promos n'est même pas entier");
                $error = true;
            }
            if(!is_numeric($event_promo->guest_number))
            {
                add_alert("Le nombre d'invités d'une des promos n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(1*$event_promo->guest_number))
            {
                add_alert("Le nombre d'invités d'une des promos n'est même pas entier");
                $error = true;
            }
        }
    }
    return !$error;
}

function are_correct_options()
{
    global $event_id, $options, $event;
    $error = false;

    foreach($options as &$option)
    {
        $option_name = isset($option->name) ? $option->name . ' : ' : "" ;
        if(!is_object($option))
        {
            add_alert("Les informations sur une des options sont mal passées. Ce n'est même pas un objet.");
            $error = true;
            continue;
        }
        if(isset($option->option_id))
        {
            if(!event_has_option(array("event_id" => $_GET['event_id'], "option_id" => $option->option_id)))
            {
                add_alert("L'id de l'option a été changée.");
                $error = true;
                continue;
            }
        }
        if(isset($option->name))
        {
            if(!is_string($option->name))
            {
                add_alert($option_name . "Le nom de l'option n'est même pas une chaine de caractères");
                $error = true;
            }
            elseif(strlen($option->name)>45)
            {
                add_alert($option_name . "Est-il nécessaire d'avoir un nom si long pour votre option ?");
                $error = true;
            }
            elseif(strlen($event->name . " Option " . $option->name)>100)
            {
                add_alert($option_name . "Le nom combiné de votre évènement et de votre option est trop grand... Enlevez quelques caractères là ou vous pouvez. La description est faire pour ça !");
                $error = true;
            }
        }
        else
        {
            $error = true;
            add_alert($option_name . "Impossible de trouver le nom de l'option");
        }
        if(isset($option->description))
        {
            if(!is_string($option->description))
            {
                add_alert($option_name . "La description de l'option n'est même pas une chaine de caractères");
                $error = true;
            }
        }
        else
        {
            add_alert($option_name . "Impossible de trouver la description de l'option");
            $error = true;
        }
        if(isset($option->quota))
        {
            if($option->quota=='')
            {
                $option->quota=null;
            }
            elseif(!is_numeric($option->quota))
            {
                add_alert($option_name . "Le quota d'une des options n'est même pas numérique");
                $error = true;
            }
            elseif(!is_an_integer(1*$option->quota))
            {
                add_alert($option_name . "Le quota d'une des options n'est même pas entier");
                $error = true;
            }
        }
        else
        {
            $error = true;
            add_alert($option_name . "Impossible de trouver le quota de l'option");
        }
        if(isset($option->is_active))
        {
            if(!in_array($option->is_active, [0,1]))
            {
                add_alert($option_name . "Les infos à propos de l'activation ou non de l'option sont mal passées.");
                $error = true;
            }
        }
        else
        {
            $error = true;
            add_alert($option_name . "Impossible de trouver l'activité ou non de l'option");
        }
        if(isset($option->type))
        {
            if($option->type=='Checkbox')
            {
                $option->is_mandatory = 0;
                if(!is_numeric($option->type_specification->price))
                {
                    add_alert($option_name . "Le prix d'une option checkbox n'est même pas numérique");
                    $error = true;
                }
                elseif(!is_an_integer(100*$option->type_specification->price))
                {
                    add_alert($option_name . "Le prix d'une option checkbox est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                    $error = true;
                }
            }
            elseif($option->type=='Select')
            {
                if(!in_array($option->is_mandatory, [0,1]))
                {
                    add_alert($option_name . "Les infos à propos de la facultativité ou non de l'option sont mal passées.");
                    $error = true;
                }
                elseif($option->is_mandatory==1)
                {
                    if(isset($option->option_id))
                    {
                        if(get_option(array('event_id' => $_GET['event_id'], 'option_id' => $option->option_id))['is_mandatory']==0)
                        {
                            if(a_participant_would_have_to_pay_obliged_option(array('event_id' => $_GET['event_id'], 'option_id' => $option->option_id)) && get_current_option_quota(array('event_id' => $event_id, 'option_id' => $option->option_id)) < $option->quota)
                            {
                                add_alert($option_name . "Il est impossible de forcer cette option à être obligatoire après que la billetterie ait commencé. En effet, certains participants ont déjà payé leur place sans prendre cette option. Si vous souhaitez tout de même faire ce changement, venez voir l'organisation de PayIcam pour en discuter.");
                                $error = true;
                            }
                        }
                        else
                        {
                            if(a_participant_would_have_to_pay_obliged_option(array('event_id' => $_GET['event_id'], 'option_id' => $option->option_id)) && get_current_option_quota(array('event_id' => $event_id, 'option_id' => $option->option_id)) < $option->quota)
                            {
                                add_alert($option_name . "Cette option était déjà obligatoire, mais il y a un problème... Un participant a accès à cette option, sa place, mais n'a pas cette option, supposée obligatoire. Contactez PayIcam si vous voyez ce message.");
                                $error = true;
                            }
                        }
                    }
                    else
                    {
                        if(participants_already_took_places($event_id))
                        {
                            add_alert($option_name . "Il est impossible d'ajouter cette option. En effet, au moins un participant a déjà une place ou une place en attente. Il faut absolument définir les options obligatoires au TOUT début. Contactez PayIcam très vite s'il faut absolument ajouter cette option OBLIGATOIRE.");
                            $error = true;
                        }
                    }
                }
                if(isset($option->type_specification))
                {
                    if(count($option->type_specification)<=1)
                    {
                        add_alert($option_name .  "Il n'y a qu'une seule option select, ce n'est pas normal. Autant utiliser une checkbox.");
                        $error = true;
                    }

                    foreach($option->type_specification as &$suboption)
                    {
                        if(!is_object($suboption))
                        {
                            add_alert($option_name .  "Les informations sur une des sous-options select sont mal passées. Ce n'est même pas un objet.");
                            $error = true;
                            continue;
                        }
                        if(isset($suboption->name))
                        {
                            if(!is_string($suboption->name))
                            {
                                add_alert($option_name .  "Le nom d'une sous-option select n'est même pas une chaine de caractères");
                                $error = true;
                            }
                            elseif(!strlen($suboption->name)>40)
                            {
                                add_alert($option_name .  "Est-il nécessaire d'avoir une sous-option si longue ?");
                                $error = true;
                            }
                            elseif(strlen($event->name . " Option " . $option->name . " Choix " . $suboption->name)>100)
                            {
                                add_alert($option_name .  "Le nom combiné de votre évènement, de votre option, et de votre sous-option est trop grand... Enlevez quelques caractères là ou vous pouvez.");
                                $error = true;
                            }
                        }
                        else
                        {
                            add_alert($option_name .  "le nom d'une sous-option n'est pas défini.");
                            $error = true;
                        }
                        if(isset($suboption->price))
                        {
                            if(!is_numeric($suboption->price))
                            {
                                add_alert($option_name .  "Le prix d'une sous-option select n'est même pas numérique");
                                $error = true;
                            }
                            elseif(!is_an_integer(100*$suboption->price))
                            {
                                add_alert($option_name .  "Le prix d'une sous-option select est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                                $error = true;
                            }
                        }
                        else
                        {
                            add_alert($option_name .  "le prix d'une sous-option n'est pas défini.");
                            $error = true;
                        }
                        if(isset($suboption->quota))
                        {
                            if(!is_numeric($suboption->quota))
                            {
                                if(in_array($suboption->quota, [null, '']))
                                {
                                    $suboption->quota = null;
                                }
                                else
                                {
                                    add_alert($option_name .  "Le quota d'une sous-option select n'est même pas numérique");
                                    $error = true;
                                }
                            }
                            elseif(!is_an_integer($suboption->quota))
                            {
                                add_alert($option_name .  "Le quota d'une sous-option select n'est pas un entier");
                                $error = true;
                            }
                        }
                        else
                        {
                            add_alert($option_name .  "le quota d'une sous-option n'est pas défini.");
                            $error = true;
                        }
                    }
                }
                else
                {
                    add_alert($option_name .  "Les sous-options ne sont pas définies.");
                    $error = true;
                }
            }
        }
        else
        {
            $error = true;
            add_alert($option_name . "Impossible de trouver le type de l'option");
        }
        if(isset($option->accessibility))
        {
            if(count($option->accessibility)==0)
            {
                add_alert($option_name . "Aucune promo n'a le droit à cette option.");
                $error = true;
            }
            foreach($option->accessibility as &$promo)
            {
                if(!is_string($promo->site))
                {
                    add_alert($option_name . "Le nom du site n'est même pas une chaine de caractères.");
                    $error = true;
                }
                else
                {
                    $promo->site_id = get_site_id($promo->site);
                    if(empty($promo->site_id))
                    {
                        add_alert($option_name . "Aucun site ne correspond au site donné. (".$promo->site.")");
                        $error = true;
                    }
                }
                if(!is_string($promo->promo))
                {
                    add_alert($option_name . "Le nom du promo n'est même pas une chaine de caractères.");
                    $error = true;
                }
                else
                {
                    $promo->promo_id = get_promo_id($promo->promo);
                    if(empty($promo->promo_id))
                    {
                        add_alert($option_name . "Aucun promo ne correspond à la promo donné. (".$promo->promo.")");
                        $error = true;
                    }
                }
            }
        }
        else
        {
            $error = true;
            add_alert($option_name . "Impossible de trouver l'accessibilité de l'option");
        }
    }
    return !$error;
}