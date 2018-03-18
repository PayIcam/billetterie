<?php

function option_form($option, $promo_id, $site_id, $participant_id=-1)
{
    $already_defined_option = get_participant_option(array("event_id" => $option['event_id'], "option_id" => $option['option_id'], "participant_id" => $participant_id));
    if(!empty($already_defined_option))
    {
        if($option['type']=='Checkbox')
        {
            checkbox_form($option, true);
        }
        elseif($option['type']=='Select')
        {
            $select_choice = json_decode($already_defined_option['option_details'])->select_option;
            select_form($option, $select_choice);
        }
    }
    else
    {
        if($option['is_active']==1)
        {
            $option_quota = $option['quota']==null ? INF : $option['quota'];
            if(get_current_option_quota(array('event_id' => $option['event_id'], 'option_id' => $option['option_id'])) < $option_quota)
            {
                if(promo_has_option(array("event_id" => $option['event_id'], "option_id" => $option['option_id'], "promo_id" => $promo_id, "site_id" => $site_id)))
                {
                    if($option['type']=='Checkbox')
                    {
                        checkbox_form($option);
                    }
                    elseif($option['type']=='Select')
                    {
                        select_form($option);
                    }
                }
            }
            else
            {
                add_error("Il n'y a plus de places pour l'option ". $option['name']. " !<br>");
            }
        }
    }
}

function participant_options_handling($event_id, $participant_id, $options)
{
    foreach($options as $option)
    {
        $option_id = $option->id;
        $option_type = $option->type;
        $option_price = $option->price;

        $option_db_data = get_option(array("event_id" => $event_id, "option_id" => $option_id));
        if($option_db_data['type']=='Checkbox')
        {
            $option_name = $option->name;
            if($option_db_data['name'] == $option_name)
            {
                if($option_price == json_decode($option_db_data['specifications'])->price)
                {
                    insert_participant_option(array("event_id" => $event_id, "participant_id" => $participant_id, "option_id" => $option_id, "option_details" => null));
                }
            }
        }
        else if($option_db_data['type']=='Select')
        {
            $option_subname = $option->name;
            $db_specifications = json_decode($option_db_data['specifications']);
            $name_found = false;
            foreach($db_specifications as $db_specification)
            {
                if($db_specification->name == $option_subname)
                {
                    $name_found = true;
                    if($option_price == $db_specification->price)
                    {
                        $option_details = json_encode(array("select_option" => $option_subname));
                        insert_participant_option(array("event_id" => $event_id, "participant_id" => $participant_id, "option_id" => $option_id, "option_details" => $option_details));
                    }
                    break;
                }
            }
        }
    }
}

function json_decode_particular($data)
{
    if($data == '')
    {
        $data = false;
    }
    else
    {
        $data = json_decode($data);
    }
    return $data;
}

function number_of_guests_to_be_displayed($promo_specifications, $guests_specifications, $current_participants_number, $total_quota, $previous_guests_number=0)
{
    $temporary_guest_number = min($promo_specifications['guest_number']-$previous_guests_number, $total_quota-$current_participants_number);
    $temporary_guest_number = $temporary_guest_number>=0 ? $temporary_guest_number : 0;

    if($temporary_guest_number + $previous_guests_number < $promo_specifications['guest_number'])
    {
        add_error("Il n'y a pas assez de places encore disponibles pour tout l'évènement pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_specifications['promo_id']). ".<br>");
    }

    $guest_quota = $guests_specifications['quota'];
    $current_guests_number = get_current_promo_quota(array('event_id' => $promo_specifications['event_id'], 'promo_id' => get_promo_id('Invités'), 'site_id' => $promo_specifications['site_id']));

    $actual_guest_number = min($temporary_guest_number, $guest_quota-$current_guests_number);
    $actual_guest_number = $actual_guest_number>=0 ? $actual_guest_number : 0;

    if($actual_guest_number < $temporary_guest_number)
    {
        add_error("Il n'y a pas assez de places encore disponibles pour les invités pour que vous ayez tous les invités que vous êtes censés avoir avec la promotion ". get_promo_name($promo_specifications['promo_id']) . ".<br>");
    }

    return $actual_guest_number;
}

function check_participant_options($participant_data, $participant_type, $event_id, $site_id, $promo_id, $error, $left_to_pay)
{
    foreach($participant_data->options as $option)
    {
        $option_id = $option->id;
        if(!is_object($option))
        {
            add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur d'une option <br>");
            $error = true;
        }
        else
        {
            if(!is_integer(intval($option_id)))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de l'id d'une option (pas entière) <br>");
                $error = true;
            }
            elseif(!promo_has_option(array("event_id" => $event_id, "option_id" => $option_id, "site_id" => $site_id, "promo_id" => $promo_id)))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de l'id d'une option (Cette promo n'a pas le droit à cette option.) <br>");
                $error = true;
            }

            $option_db_data = get_option(array("event_id" => $event_id, "option_id" => $option_id));

            if($option_db_data['is_active']==0)
            {
                add_error($participant_type . " : Option ". $option_db_data['name'] . " L'option n'est pas active...<br>".$option_id);
                $error = true;
            }

            if($option->type != $option_db_data['type'])
            {
                add_error($participant_type . " : Option ". $option_db_data['name'] . " Quelqu'un s'est débrouillé pour altérer la valeur du type d'une option".$option_id);
                $error = true;
            }
            else
            {
                if(get_current_option_quota(array("event_id" => $event_id, "option_id" => $option_id)) +1 > $option_db_data['quota'])
                {
                    add_error($participant_type . " : Option ". $option_db_data['name'] . " : Il n'y a plus de places disponibles pour cette option. <br>");
                    $error = true;
                }
                if($option_db_data['type']=='Checkbox')
                {
                    if($option_db_data['name'] == $option->name)
                    {
                        if($option->price == json_decode($option_db_data['specifications'])->price)
                        {
                            // add_error($participant_type . " : Checkbox option correcte <br>");
                            if($left_to_pay!=false)
                            {
                                $left_to_pay-=$option->price;
                            }
                        }
                        else
                        {
                            add_error($participant_type . " : ". $option_db_data['name'] . " : Quelqu'un s'est débrouillé pour altérer la valeur du prix d'une option checkbox <br>");
                            $error = true;
                        }
                    }
                    else
                    {
                        add_error($participant_type . " : Option ". $option_db_data['name'] . " : Quelqu'un s'est débrouillé pour altérer la valeur du nom d'une option checkbox <br>");
                        $error = true;
                    }
                }
                else if($option_db_data['type']=='Select')
                {
                    $option_subname = $option->name;
                    $db_specifications = json_decode($option_db_data['specifications']);
                    $name_found = false;
                    foreach($db_specifications as $db_specification)
                    {
                        if($db_specification->name == $option_subname)
                        {
                            if(get_current_select_option_quota(array("event_id" => $event_id, "option_id" => $option_id, "subname" => $db_specification->name))+1 > $db_specification->quota)
                            {
                                add_error($participant_type . " : Option ". $option_db_data['name'] . " : Le quota d'une sous-option est déjà plein. <br>");
                                $error = true;
                            }

                            $name_found = true;
                            if($option->price == $db_specification->price)
                            {
                                // add_error($participant_type . " : Select option correcte <br>");
                                if($left_to_pay!=false)
                                {
                                    $left_to_pay-=$option->price;
                                }
                            }
                            else
                            {
                                add_error($participant_type . " : Option ". $option_db_data['name'] . " : Quelqu'un s'est débrouillé pour altérer la valeur du prix d'une sous-option select <br>");
                                $error = true;
                            }
                            break;
                        }
                    }
                    if($name_found == false)
                    {
                        add_error($participant_type . " : Option ". $option_db_data['name'] . " : Quelqu'un s'est débrouillé pour altérer la valeur du nom d'une sous-option select <br>");
                        $error = true;
                    }
                }
                else
                {
                    add_error($participant_type . " : Option ". $option_db_data['name'] . " : Quelqu'un s'est débrouillé pour mettre un type qui n'est ni 'Select' ni 'Checkbox' dans le champ type de la table option de la base de données <br>");
                    $error = true;
                }
            }
        }
    }
    return ["error" => $error, "left_to_pay" => $left_to_pay];
}


function is_correct_participant_data($participant_data, $participant_type, $promo_specifications)
{
    $event_id = $promo_specifications['event_id'];
    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $_SESSION['icam_informations']->promo_id;
    $site_id = $_SESSION['icam_informations']->site_id;
    $prenom = $_SESSION['icam_informations']->prenom;
    $nom = $_SESSION['icam_informations']->nom;

    $error = false;
    if($participant_data == null)
    {
        add_error($participant_type . " : POST['".$participant_type."_informations'] est mal défini. Il est impossible de le décoder. <br>");
        $error = true;
    }
    else
    {
        $participant_data_length = $participant_type=='icam' ? 10:8;
        if(count(get_object_vars($participant_data)) != $participant_data_length)
        {
            add_error($participant_type . " : Il n'y a pas le bon nombre d'éléments dans l'objet. <br>");
            $error = true;
        }
        else
        {
            $participant_data_is_icam = $participant_type=='icam' ? 1:0;
            $participant_data_promo_id = $participant_type=='icam' ? $promo_id : get_promo_id('Invités');
            $left_to_pay = $participant_data->price-$promo_specifications['price'];

            if($participant_data->is_icam != $participant_data_is_icam)
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de is_icam <br>");
                $error = true;
            }
            if($participant_data->site_id != $site_id)//Faire avec les variables de session
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de site_id <br>");
                $error = true;
            }
            if($participant_data->promo_id != $participant_data_promo_id)//Faire avec les variables de session
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de promo_id <br>");
                $error = true;
            }
            if(!is_numeric($participant_data->price))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prix (pas numérique)<br>");
                $left_to_pay=false;
                $error = true;
            }
            elseif($participant_data->price < $promo_specifications['price'])
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prix (inférieur au prix de base de données)<br>");
                $left_to_pay=false;
                $error = true;
            }
            if($participant_type=='icam')
            {
                if($participant_data->prenom != $prenom)//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prénom <br>");
                    $error = true;
                }
                if($participant_data->nom != $nom)//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du nom <br>");
                    $error = true;
                }
                if($participant_data->email != $email)//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de l'email <br>");
                    $error = true;
                }
                if(participant_has_its_place(array("event_id" => $event_id, "email" => $email, "promo_id" => $promo_id, "site_id" => $site_id, "email" => $email)))
                {
                    add_error("Vous avez déjà une réservation enregistrée à votre email.");
                    $error = true;
                }
                if(!is_string($participant_data->telephone))
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du numéro de téléphone <br>");
                    $error = true;
                }
                elseif(count($participant_data->telephone)>25)
                {
                    add_error($participant_type . " : Pourquoi avez vous besoin d'autant de caractères pour un simple numéro de téléphone ?<br>");
                    $error = true;
                }
            }
            elseif($participant_type=='icam')
            {
                if(!is_string($participant_data->prenom))//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prénom <br>");
                    $error = true;
                }
                elseif(count($participant_data->prenom)>100)
                {
                    add_error($participant_type . " : Le prenom a-t-il besoin d'être si long ?<br>");
                }
                if(!is_string($participant_data->nom))//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du nom <br>");
                    $error = true;
                }
                elseif(count($participant_data->nom)>100)
                {
                    add_error($participant_type . " : Le nom a-t-il besoin d'être si long ?<br>");
                }
            }
            if(!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $participant_data->birthdate) and $participant_data->birthdate!='')
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de la date de naissance <br>");
                $error = true;
            }
            if(!is_array($participant_data->options))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur des options <br>");
                $error = true;
            }
            elseif(count($participant_data->options)>0)
            {
                $res = check_participant_options($participant_data, $participant_type, $event_id, $site_id, $promo_id, $error, $left_to_pay);
                $error = $res['error'];
                $left_to_pay = $res['left_to_pay'];
            }
            if($left_to_pay!=0)
            {
                $error = true;
                add_error("Le prix total n'est pas bon.");
            }
            else
            {
                global $total_price;
                $total_price+=$participant_data->price;
            }
        }
    }
    return !$error;
}
function is_correct_participant_supplement_data($participant_data, $participant_type, $promo_specifications)
{
    $event_id = $promo_specifications['event_id'];
    $email = $_SESSION['icam_informations']->mail;
    $promo_id = $_SESSION['icam_informations']->promo_id;
    $site_id = $_SESSION['icam_informations']->site_id;
    $prenom = $_SESSION['icam_informations']->prenom;
    $nom = $_SESSION['icam_informations']->nom;

    $error = false;
    if($participant_data == null)
    {
        add_error($participant_type . " : POST['".$participant_type."_informations'] est mal défini. Il est impossible de le décoder. <br>");
        $error = true;
    }
    else
    {
        $participant_data_length = $participant_type=='icam' ? 7:8;
        if(count(get_object_vars($participant_data)) != $participant_data_length)
        {
            add_error($participant_type . " : Il n'y a pas le bon nombre d'éléments dans l'objet. <br>");
            $error = true;
        }
        else
        {
            $participant_data_promo_id = $participant_type=='icam' ? $promo_id : get_promo_id('Invités');
            $left_to_pay = $participant_data->price;

            if($participant_data->site_id != $site_id)//Faire avec les variables de session
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de site_id <br>");
                $error = true;
            }
            if($participant_data->promo_id != $participant_data_promo_id)//Faire avec les variables de session
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de promo_id <br>");
                $error = true;
            }
            if(!is_numeric($participant_data->price))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prix (pas numérique)<br>");
                $left_to_pay=false;
                $error = true;
            }
            if($participant_type=='icam')
            {
                if(!is_string($participant_data->telephone))
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du numéro de téléphone <br>");
                    $error = true;
                }
                elseif(count($participant_data->telephone)>25)
                {
                    add_error($participant_type . " : Pourquoi avez vous besoin d'autant de caractères pour un simple numéro de téléphone ?<br>");
                    $error = true;
                }
            }
            elseif($participant_type=='guests')
            {
                if(!is_string($participant_data->prenom))//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du prénom <br>");
                    $error = true;
                }
                elseif(count($participant_data->prenom)>100)
                {
                    add_error($participant_type . " : Le prenom a-t-il besoin d'être si long ?<br>");
                }

                if(!is_string($participant_data->nom))//Faire avec les variables de session
                {
                    add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur du nom <br>");
                    $error = true;
                }
                elseif(count($participant_data->nom)>100)
                {
                    add_error($participant_type . " : Le nom a-t-il besoin d'être si long ?<br>");
                }
            }
            if(!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $participant_data->birthdate) and $participant_data->birthdate!='')
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur de la date de naissance <br>");
                $error = true;
            }
            if(!is_array($participant_data->options))
            {
                add_error($participant_type . " : Quelqu'un s'est débrouillé pour altérer la valeur des options <br>");
                $error = true;
            }
            elseif(count($participant_data->options)>0)
            {
                $res = check_participant_options($participant_data, $participant_type, $event_id, $site_id, $promo_id, $error, $left_to_pay);
                $error = $res['error'];
                $left_to_pay = $res['left_to_pay'];
            }
            if($left_to_pay!=0)
            {
                $error = true;
                add_error("Le prix total n'est pas bon.");
            }
            else
            {
                global $total_price;
                $total_price+=$participant_data->price;
            }
        }
    }
    return !$error;
}

function get_icams_guests_data($ids)
{
    $guests_ids = get_icams_guests_ids($ids);
    $guests_data = [];
    foreach($guests_ids as $guests_id)
    {
        $guest_data = get_participant_event_data(array("event_id" => $ids['event_id'], "participant_id" => $guests_id['guest_id']));
        array_push($guests_data, $guest_data);
    }
    return $guests_data;
}

function prevent_displaying_on_wrong_ticketing_state($ticketing_state)
{
    if(in_array($ticketing_state, array('coming in some time', 'coming soon', 'ended long ago', 'ended and no reservation')))
    {
        switch ($ticketing_state)
        {
            case 'coming soon':
            case 'coming in some time':
            {
                set_alert_style();
                add_error("La billetterie n'a pas encore commencé.");
                die();
            }
            case 'ended and no reservation':
            case 'ended long ago':
            {
                set_alert_style();
                add_error("La billetterie est finie.");
                die();
            }
        }
    }
}

function check_if_event_should_be_displayed($event,$promo_id, $site_id, $email)
{
    $event_id = $event['event_id'];
    if($event['is_active']==0)
    {
        set_alert_style();
        add_error("L'évènement n'est pas encore actif");
        die();
    }

    $icam_has_reservation = participant_has_its_place(array("event_id" => $event_id, "promo_id" => $promo_id, "site_id" => $site_id, "email" => $email));
    $ticketing_state = get_ticketing_state($event, $promo_id, $site_id, $email, $icam_has_reservation);

    prevent_displaying_on_wrong_ticketing_state($ticketing_state);
}