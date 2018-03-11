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