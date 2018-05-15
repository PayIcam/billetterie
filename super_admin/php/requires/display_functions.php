<?php

function display_folder_activity_edition($folder)
{
    ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-4">
                <label for="<?=$folder['folder']?>_available">Tout <?=$folder['folder']?> est-t-il activé ?</label>
            </div>
            <div class="col-sm-1 col-sm-offset-7">
                <input id="<?=$folder['folder']?>_available" name="<?=$folder['folder']?>_available" type="checkbox" data-toggle="toggle" data-on="Activer" data-off="Désactiver" value=1>
            </div>
            <?= $folder['is_active']==1 ? '<script> $("#' . $folder['folder'] . '_available").click(); </script>' : "" ?>
        </div>
    </div>
    <?php
}
