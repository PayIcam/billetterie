<?php

function prepare_participant_displaying($participant)
{
    global $event_id;

    $participant['promo'] = get_promo_name($participant['promo_id']);
    $participant['site'] = get_site_name($participant['site_id']);

    $participant['promo_guest_number'] = get_promo_guest_number(array("event_id" => $event_id, "promo_id" => $participant['promo_id'], "site_id" => $participant['site_id']));
    $guest_numbers_by_status = get_current_guest_number_by_status($participant['participant_id']);

    $participant['validated_guest_number'] = 0;
    $participant['waiting_guest_number'] = 0;
    $participant['cancelled_guest_number'] = 0;
    foreach($guest_numbers_by_status as $guest)
    {
        switch($guest['status'])
        {
            case 'V':
                $participant['validated_guest_number'] = $guest['guest_number'];
                break;
            case 'W':
                $participant['waiting_guest_number'] = $guest['guest_number'];
                break;
            case 'A':
                $participant['cancelled_guest_number'] = $guest['guest_number'];
                break;
        }
    }
    $participant['current_promo_guest_number'] = $participant['validated_guest_number'] . "/" . $participant['promo_guest_number'];

    $participant['validated_options'] = get_participant_options(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));
    $participant['pending_options'] = get_pending_options(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));

    return $participant;
}

function check_update_participant_data($data, $is_icam)
{
    $error = false;
    $post_inputs = $is_icam == 0 ? 3:1;
    if(count($data) == $post_inputs)
    {
        if($is_icam==0)
        {
            if(isset($data['prenom']))
            {
                if(count($data['prenom']) > 45)
                {
                    $error = true;
                    add_error('Le prénom entré est trop grand');
                }
            }
            else
            {
                $error = true;
                add_error('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
            }
            if(isset($data['nom']))
            {
                if(count($data['nom']) > 45)
                {
                    $error = true;
                    add_error('Le nom entré est trop grand');
                }
            }
            else
            {
                $error = true;
                add_error('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
            }
        }
        if(isset($data['bracelet_identification']))
        {
            $data['bracelet_identification'] = $data['bracelet_identification'] == '' ? null : $data['bracelet_identification'];
            if(count($data['bracelet_identification']) > 25)
            {
                $error = true;
                add_error('Le bracelet_identification entré est trop grand');
            }
            elseif(!bracelet_identification_is_available(array('bracelet_identification' => $data['bracelet_identification'], 'event_id' => $_GET['event_id'], 'participant_id' => $_GET['participant_id'])))
            {
                $error = true;
                add_error('Ce bracelet est déjà pris.');
            }
        }
        else
        {
            $error = true;
            add_error('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
        }
    }
    else
    {
        $error = true;
        add_error("On s'amuse bien ?");
    }
    return !$error;
}

function check_prepare_addition_data($data, $icam_site_id)
{
    global $promos, $sites;

    $error = false;
    $data_nb_inputs = $icam_site_id === false ? 9 : 5;

    if(count($data)==$data_nb_inputs)
    {
        if(isset($data['prenom']))
        {
            if(count($data['prenom']) > 45)
            {
                $error = true;
                add_error_to_ajax_response('Le prénom de votre nouveau participant est trop long.');
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("Le prénom n'est pas défini.");
        }
        if(isset($data['nom']))
        {
            if(count($data['nom']) > 45)
            {
                $error = true;
                add_error_to_ajax_response('Le nom de votre nouveau participant est trop long.');
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("Le nom n'est pas défini.");
        }
        if($icam_site_id === false)
        {
            if(isset($data['telephone']))
            {
                if(!count($data['telephone']) > 25)
                {
                    $error = true;
                    add_error_to_ajax_response('Le numéro de téléphone de votre nouveau participant est trop long.');
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("Le numéro de télephone n'est pas défini.");
            }
            if(isset($data['email']))
            {
                if(!count($data['email']) > 255)
                {
                    $error = true;
                    add_error_to_ajax_response("L'email de votre nouveau participant est trop long.");
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("L'email de votre participant n'est pas défini.");
            }
        }
        if(isset($data['bracelet_identification']))
        {
            $data['bracelet_identification'] = $data['bracelet_identification'] == '' ? null : $data['bracelet_identification'];
            if(count($data['bracelet_identification']) > 25)
            {
                $error = true;
                add_error_to_ajax_response("L'identifiant de bracelet de votre nouveau participant est trop long.");
            }
            elseif(!bracelet_identification_is_available(array('bracelet_identification' => $data['bracelet_identification'], 'event_id' => $_GET['event_id'])))
            {
                $error = true;
                add_error_to_ajax_response('Ce bracelet est déjà pris.');
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("L'identifiant du bracelet n'est pas défini.");
        }
        if(isset($data['price']))
        {
            if(is_numeric($data['price']))
            {
                if(floor(100*$data['price']) != 100*$data['price'])
                {
                    add_error_to_ajax_response("Le prix est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                    $error = true;
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("Le prix de votre nouveau participant n'est pas numérique.");
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("Le prix n'est pas défini.");
        }
        if(isset($data['payement']))
        {
            if(is_string($data['payement']))
            {
                if(!count($data['payement'])>45)
                {
                    $error = true;
                    add_error_to_ajax_response("Non seulement vous avez changé les propositions, mais en plus, vous avez mit trop de caractères.");
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("Non seulement vous avez changé les propositions, mais en plus, vous avez mit trop de caractères.");
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("Le moyen de payement n'est pas défini.");
        }
        if($icam_site_id === false)
        {
            if(isset($data['promo']))
            {
                if(in_array($data['promo'], $promos))
                {
                    $_POST['promo_id'] = get_promo_id($data['promo']);
                }
                else
                {
                    $error = true;
                    add_error_to_ajax_response("La promo n'a pas été reconnue.");
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("L'identifiant du bracelet n'est pas défini.");
            }
            if(isset($data['site']))
            {
                if(in_array($data['site'], $sites))
                {
                    $_POST['site_id'] = get_site_id($data['site']);
                }
                else
                {
                    $error = true;
                    add_error_to_ajax_response("Le site n'a pas été reconnu.");
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("L'identifiant du bracelet n'est pas défini.");
            }
        }
        else
        {
            $_POST['promo_id'] = get_promo_id('Invités');
            $_POST['site_id'] = get_site_id($icam_site_id);
            $_POST['email'] = null;
            $_POST['telephone'] = null;
        }
    }
    else
    {
        $error = true;
        add_error_to_ajax_response("Il n'y a pas le bon nombre de données transmises.");
    }
    return !$error;
}

function display_option_no_checking($option)
{
    if($option['type']=='Checkbox')
    {
        checkbox_form_basic($option);
    }
    elseif($option['type']=='Select')
    {
        select_form_basic($option);
    }
}

function check_prepare_option_addition_data($options, $participant_id)
{
    $promo_site_ids = get_participant_promo_site_ids(array('event_id' => $_GET['event_id'], 'participant_id' => $participant_id));

    $promo_id = $promo_site_ids['promo_id'];
    $site_id = $promo_site_ids['site_id'];

    $error = false;
    foreach($options as $option)
    {
        if(option_can_be_added(array('promo_id' => $promo_id, 'site_id' => $site_id, 'option_id' => $option['option_id'], 'event_id' => $_GET['event_id'])))
        {
            if(!participant_has_option(array('participant_id' => $participant_id, 'option_id' => $option['option_id'], 'event_id' => $_GET['event_id'])))
            {
                $db_option = get_option(array('option_id' => $option['option_id'], 'event_id' => $_GET['event_id']));
                if($db_option['type']==$option['type'])
                {
                    if($db_option['type']=='Select')
                    {
                        $found=false;
                        foreach(json_decode($db_option['specifications']) as $specification)
                        {
                            if($specification->name == $option['complement'])
                            {
                                $found=true;
                                break;
                            }
                        }
                        if(!$found)
                        {
                            $error=true;
                            add_error_to_ajax_response("Le nom de la sous-option est faux");
                        }
                    }
                }
                else
                {
                    $error = true;
                    add_error_to_ajax_response("Le type indiqué est faux.");
                }
            }
            else
            {
                $error = true;
                add_error_to_ajax_response("Le participant a déjà cette option.");
            }
        }
        else
        {
            $error = true;
            add_error_to_ajax_response("Vous n'etes pas censé ajouter cette option.");
        }
    }
    return !$error;
}

function prepare_promo_stats($promo_stats, $current_participant_number)
{
    $promo_stats['promo_name'] = get_promo_name($promo_stats['promo_id']);
    $promo_stats['site_name'] = get_site_name($promo_stats['site_id']);
    $promo_stats['pourcentage_quota'] = round(100 * $promo_stats['promo_count'] / $promo_stats['quota'], 2) . '%';
    $promo_stats['pourcentage_evenement'] = round(100 * $promo_stats['promo_count'] / $total_quota, 2) . '%';
    $promo_stats['pourcentage_bracelet'] = round(100 * $promo_stats['bracelet_count'] / $promo_stats['promo_count'], 2) . '%';
    return $promo_stats;
}
