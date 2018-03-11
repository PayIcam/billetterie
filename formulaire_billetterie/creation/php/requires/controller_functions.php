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