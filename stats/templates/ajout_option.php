                <div id="options">
                    <h2>Choisissez les options du participant.</h2>
                    <div class="row">
                        <?php
                        foreach($options as $option)
                        {
                            $option['specifications'] = json_decode($option['specifications']);
                            option_form($option, , $guest_specifications['site_id'], $guest_id);
                        }
                        ?>
                    </div>
                </div>