<?php

function option_form($option, $ids)
{
    if(promo_has_option($ids))
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

function participant_options_handling($event_id, $participant_id, $options)
{
    foreach($options as $option)
    {
        $option_id = $option->id;
        $option_type = $option->type;
        $option_price = $option->price;

        $option_db_data = get_option(array("event_id" => $event_id, "option_id" => $option_id));
        if($option_db_data!=false)
        {
            if($option_type != $option_db_data['type'])
            {
                echo "Something is very wrong... Types aren't identical. Option_id is ".$option_id;
            }
            else
            {
                if($option_db_data['type']=='Checkbox')
                {
                    $option_name = $option->name;
                    if($option_db_data['name'] == $option_name)
                    {
                        if($option_price == json_decode($option_db_data['specifications'])->price)
                        {
                            insert_participant_option(array("event_id" => $event_id, "participant_id" => $participant_id, "option_id" => $option_id, "option_details" => null));
                        }
                        else
                        {
                            echo "Something is very wrong... Prices aren't identical";
                        }
                    }
                    else
                    {
                        echo "Something is very wrong... Names aren't identical";
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
                            else
                            {
                                echo "Something is very wrong... Select prices aren't identical";
                            }
                            break;
                        }
                    }
                    if($name_found == false)
                    {
                        echo "Something is very wrong... Select option was never found";
                    }
                }
                else
                {
                    echo "Database was altered in someway. (type not either 'Select' or 'Checkbox' yet both identical)";
                }
            }
        }
        else
        {
            echo 'PDO query to get the option returned false. The user most likely changed the option_id, and it did not exist anymore';
        }
    }
}