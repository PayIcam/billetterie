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
                    var_dump(count($data['prenom']));
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
                    var_dump(count($data['nom']));
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
                var_dump(count($data['bracelet_identification']));
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