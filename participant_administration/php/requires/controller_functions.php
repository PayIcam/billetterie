<?php

/**
 * Vérifier que les informations entrées sur un edit d'une participant sont bonnes
 * @param  [tinyint] $is_icam [1 ou 0]
 * @return [boolean]          [true si tout est bon]
 */
function check_update_participant_data($is_icam)
{
    $error = false;
    $post_inputs = $is_icam == 0 ? 3:1;
    if(count($_POST) == $post_inputs)
    {
        if($is_icam==0)
        {
            if(isset($_POST['prenom']))
            {
                $_POST['prenom'] = htmlspecialchars($_POST['prenom']);
                if(count($_POST['prenom']) > 45)
                {
                    $error = true;
                    add_alert('Le prénom entré est trop grand');
                }
            }
            else
            {
                $error = true;
                add_alert('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
            }
            if(isset($_POST['nom']))
            {
                $_POST['nom'] = htmlspecialchars($_POST['nom']);
                if(count($_POST['nom']) > 45)
                {
                    $error = true;
                    add_alert('Le nom entré est trop grand');
                }
            }
            else
            {
                $error = true;
                add_alert('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
            }
        }
        if(isset($_POST['bracelet_identification']))
        {
            $_POST['bracelet_identification'] = $_POST['bracelet_identification'] == '' ? null : htmlspecialchars($_POST['bracelet_identification']);
            if(count($_POST['bracelet_identification']) > 25)
            {
                $error = true;
                add_alert('Le bracelet_identification entré est trop grand');
            }
            elseif(!bracelet_identification_is_available(array('bracelet_identification' => $_POST['bracelet_identification'], 'event_id' => $_GET['event_id'], 'participant_id' => $_GET['participant_id'])))
            {
                $error = true;
                add_alert('Ce bracelet est déjà pris.');
            }
        }
        else
        {
            $error = true;
            add_alert('Vous avez bidouillé le formulaire, et il manque des attributs, en êtes vous fier ? ');
        }
    }
    else
    {
        $error = true;
        add_alert("On s'amuse bien ?");
    }
    return !$error;
}

/**
 * Vérifier que les infos sur un ajout de participant sont bonnes
 * @param  [mixed] $icam_site_id [le site_id du l'Icam si c'en est un, false sinon]
 * @return [type]               [description]
 */
function check_prepare_addition_data($icam_site_id)
{
    global $promos, $sites, $mandatory_choices;
    $error = false;
    $icam_nb_inputs = 8;
    $guest_nb_inputs = 5;

    if(isset($_POST['choice_ids'])) {
        if(is_array($_POST['choice_ids'])) {
            $icam_nb_inputs += 2;
            $guest_nb_inputs += 2;
        } else {
            $error = true;
            add_alert_to_ajax_response('Les options reçues ont été altérées.');
        }
    }

    $post_nb_inputs = $icam_site_id === false ? $icam_nb_inputs : $guest_nb_inputs;

    if(count($_POST)==$post_nb_inputs)
    {
        if(isset($_POST['prenom']))
        {
            $_POST['prenom'] = htmlspecialchars($_POST['prenom']);
            if(strlen($_POST['prenom']) > 45)
            {
                $error = true;
                add_alert_to_ajax_response('Le prénom de votre nouveau participant est trop long.');
            }
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("Le prénom n'est pas défini.");
        }
        if(isset($_POST['nom']))
        {
            $_POST['nom'] = htmlspecialchars($_POST['nom']);
            if(strlen($_POST['nom']) > 45)
            {
                $error = true;
                add_alert_to_ajax_response('Le nom de votre nouveau participant est trop long.');
            }
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("Le nom n'est pas défini.");
        }
        if($icam_site_id === false)
        {
            if(isset($_POST['email']))
            {
                if(strlen($_POST['email']) <= 255) {
                    if(participant_already_has_place(array('event_id' => $_GET['event_id'], 'email' => $_POST['email']))) {
                        $error = true;
                        add_alert_to_ajax_response("Ce participant a déjà pris sa place");
                    }
                } else {
                    $error = true;
                    add_alert_to_ajax_response("L'email de votre nouveau participant est trop long.");
                }
            }
            else
            {
                $error = true;
                add_alert_to_ajax_response("L'email de votre participant n'est pas défini.");
            }
        }
        if(isset($_POST['bracelet_identification']))
        {
            $_POST['bracelet_identification'] = $_POST['bracelet_identification'] == '' ? null : htmlspecialchars($_POST['bracelet_identification']);
            if(strlen($_POST['bracelet_identification']) > 25)
            {
                $error = true;
                add_alert_to_ajax_response("L'identifiant de bracelet de votre nouveau participant est trop long.");
            }
            elseif(!bracelet_identification_is_available(array('bracelet_identification' => $_POST['bracelet_identification'], 'event_id' => $_GET['event_id'])))
            {
                $error = true;
                add_alert_to_ajax_response('Ce bracelet est déjà pris.');
            }
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("L'identifiant du bracelet n'est pas défini.");
        }
        if(isset($_POST['price']))
        {
            if(is_numeric($_POST['price']))
            {
                if(floor(100*$_POST['price']) != 100*$_POST['price'])
                {
                    add_alert_to_ajax_response("Le prix est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                    $error = true;
                }
            }
            else
            {
                $error = true;
                add_alert_to_ajax_response("Le prix de votre nouveau participant n'est pas numérique.");
            }
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("Le prix n'est pas défini.");
        }
        if(isset($_POST['payement']))
        {
            if(!in_array($_POST['payement'], ["Espèces", "Mozart", "Carte bleue", "Pumpkin", "Lydia", "Circle", "Offert", "à l'amiable", "Autre"]))
            {
                $error = true;
                add_alert_to_ajax_response("Le moyen de payement n'est pas dans la liste");
            }
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("Le moyen de payement n'est pas défini.");
        }
        if($icam_site_id === false)
        {
            if(isset($_POST['promo']))
            {
                if(in_array($_POST['promo'], $promos))
                {
                    $_POST['promo_id'] = get_promo_id($_POST['promo']);
                }
                else
                {
                    $error = true;
                    add_alert_to_ajax_response("La promo n'a pas été reconnue.");
                }
            }
            else
            {
                $error = true;
                add_alert_to_ajax_response("La promo n'est pas définie.");
            }
            if(isset($_POST['site']))
            {
                if(in_array($_POST['site'], $sites))
                {
                    $_POST['site_id'] = get_site_id($_POST['site']);
                }
                else
                {
                    $error = true;
                    add_alert_to_ajax_response("Le site n'a pas été reconnu.");
                }
            }
            else
            {
                $error = true;
                add_alert_to_ajax_response("Le site n'est pas définie.");
            }
        }
        else
        {
            $_POST['promo_id'] = get_promo_id('Invités');
            $_POST['site_id'] = get_site_id($icam_site_id);
            $_POST['email'] = null;
        }
    }
    else
    {
        $error = true;
        add_alert_to_ajax_response("Il n'y a pas le bon nombre de données transmises.");
    }
    return !$error;
}

/**
 * Ajustement de option_form pour ne rien vérifier.
 * @param  [array] $option [fetch de options avec option['option_choices'] qui contient un fetchAll des option_choices associés]
 */
function display_option_no_checking($option, $mandatory=false)
{
    if($option['type']=='Checkbox')
    {
        $option['option_choices'] = $option['option_choices'][0];
        checkbox_form_basic($option);
    }
    elseif($option['type']=='Select')
    {
        select_form_basic($option, $mandatory);
    }
}

/**
 * Permet de vérifier que les informations entrées sur un ajout d'options sont bonnes
 * @param  boolean $check_basic_too Permet de savoir si on est dans un ajout d'options obligatoire (ajout_participant.php) ou dans un ajout d'options facultatives (ajout_options.php)
 * @return mixed false si il y a eu des erreurs, sinon choice_datas, pour les options à ajouter.
 */
function check_prepare_option_choice_data($check_basic_too=true)
{
    if($check_basic_too) {
        $promo_site_ids = get_participant_promo_site_ids(array('event_id' => $_GET['event_id'], 'participant_id' => $_GET['participant_id']));

        $is_mandatory = 0;
        $promo_id = $promo_site_ids['promo_id'];
        $site_id = $promo_site_ids['site_id'];
    } else {
        $is_mandatory = 1;
        $promo_id = $_POST['promo_id'];
        $site_id = $_POST['site_id'];
        $option_ids = [];
    }

    $error = false;

    $choice_datas = array();

    if($check_basic_too) {
        $_POST['choice_ids'] = array_column($_POST['choice_ids'], 'choice_id');
    }

    foreach($_POST['choice_ids'] as $choice_id)
    {
        if(participant_can_have_choice(array('choice_id' => $choice_id, 'promo_id' => $promo_id, 'site_id' => $site_id)))
        {
            $choice_data = get_option_choice($choice_id);
            if(option_can_be_added(array('promo_id' => $promo_id, 'site_id' => $site_id, 'option_id' => $choice_data['option_id'], 'event_id' => $_GET['event_id'], 'is_mandatory' => $is_mandatory)))
            {
                if($check_basic_too) {
                    if(!participant_has_option(array('participant_id' => $_GET['participant_id'], 'option_id' => $choice_data['option_id'], 'event_id' => $_GET['event_id'])))
                    {
                        if(isset($_POST['payement']))
                        {
                            if(!in_array($_POST['payement'], ["Espèces", "Mozart", "Carte bleue", "Pumpkin", "Lydia", "Circle", "Offert", "à l'amiable", "Autre"]))
                            {
                                $error = true;
                                add_alert_to_ajax_response("Le moyen de payement n'est pas dans la liste");
                            }
                        }
                        else
                        {
                            $error = true;
                            add_alert_to_ajax_response("Le moyen de payement n'est pas spécifié");
                        }
                        if(isset($_POST['price']))
                        {
                            if(is_numeric($_POST['price']))
                            {
                                if(floor(100*$_POST['price']) != 100*$_POST['price'])
                                {
                                    add_alert_to_ajax_response("Le prix est défini avec une précision plus grande que le centime, ou n'est même pas positif");
                                    $error = true;
                                }
                            }
                            else
                            {
                                $error = true;
                                add_alert_to_ajax_response("Le prix de votre nouveau participant n'est pas numérique.");
                            }
                        }
                        else
                        {
                            $error = true;
                            add_alert_to_ajax_response("Les informations à propos du prix ne sont pas passées");
                        }
                    }
                    else
                    {
                        $error = true;
                        add_alert_to_ajax_response("Le participant a déjà cette option.");
                    }
                } else {
                    if(!in_array($choice_data['option_id'], $option_ids)) {
                        array_push($option_ids, $choice_data['option_id']);
                    } else {
                        $error = true;
                        add_alert_to_ajax_response("Bah voyons donc, t'essayes tu d'ajouter plusieurs choix de la même option ?");
                    }
                }
            }
            else
            {
                $error = true;
                add_alert_to_ajax_response("Vous n'etes pas censé ajouter cette option.");
            }
            $choice_datas[] = $choice_data;
        }
        else
        {
            $error = true;
            add_alert_to_ajax_response("Ce participant ne peux pas avoir ces options.");
        }
    }
    return $error == true ? false : $choice_datas;
}
/**
 * Prepare seulement la définition des pourcentages des stats d'une promo
 * @param  [array] $promo_stats
 * @return [array] $promo_stats
 */
function prepare_promo_stats($promo_stats)
{
    global $event_details_stats;
    $promo_stats['pourcentage_quota'] = $promo_stats['quota']!=0 ? round(100 * $promo_stats['promo_count'] / $promo_stats['quota'], 2) . '%' : "undefined";
    $promo_stats['pourcentage_evenement'] = $event_details_stats['total_count']!=0 ? round(100 * $promo_stats['promo_count'] / $event_details_stats['total_count'], 2) . '%' : "0%";
    $promo_stats['pourcentage_bracelet'] = $promo_stats['promo_count']!=0 ? round(100 * $promo_stats['bracelet_count'] / $promo_stats['promo_count'], 2) . '%' : "0%";
    $promo_stats['pourcentage_invites'] = $event_details_stats['guests_count']!=0 ? round(100 * $promo_stats['invited_guests'] / $event_details_stats['guests_count'], 2) . '%' : "0%";
    return $promo_stats;
}

/**
 * Permet de préparer les données du participant, pour afficher ses informations sur une ligne
 * @param  [array] $participant [fetch de participant]
 * @return [array] $participan
 */
function prepare_participant_displaying($participant)
{
    global $event_id;

    $participant['is_in'] = participant_has_arrived($participant['participant_id']);
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

    $participant['validated_options'] = get_participant_options_and_choices(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));
    $participant['pending_options'] = get_pending_options_and_choices(array("event_id" => $event_id, "participant_id" => $participant['participant_id']));

    return $participant;
}