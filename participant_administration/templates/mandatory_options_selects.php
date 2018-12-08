<div id="options">
    <h2 class="text-center">Choisissez les options obligatoires du participant</h2>
    <div class="row" id="mandatory_options_selects">
        <?php
        foreach($mandatory_options as $option)
        {
            $option['option_choices'] = get_option_choices($option['option_id']);
            display_option_no_checking($option, true);
        }
        ?>
    </div>
</div>
