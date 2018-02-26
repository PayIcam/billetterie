<div class="all_parametres">
    <div class="tableau_quota">
        <legend style="font-size:1.8em;"><strong>Quotas type de places</strong></legend>
        <table class="table table-bordered" style="background-color: #ffffff;">
            <thead>
                <tr>
                    <th class="case_tableau_1">Place</th>
                    <th class="case_tableau_1">Actuellement</th>
                    <th class="case_tableau_1">Pourcentage</th>
                    <th class="case_tableau_1">Quotas</th>
                </tr>
            </thead>
            <tbody>
            <?php
            for($i=0; $i<=5; $i++)
            {
                $row = $status[$i];
            ?>
                <tr>
                    <td class="case_tableau_1"><label for="row"><?php echo htmlspecialchars($row['name']) ?></label></td>
                    <td class="case_tableau_1"><?php echo htmlspecialchars($row['current']);  ?> </td>
                    <td class="case_tableau_1"><?php $pourcentage =$row['current']/$row['value'];
                        $pourcentage = round(100*$pourcentage,2);
                        $pourcentage = color_percentage($pourcentage);
                        echo $pourcentage; ?> </td>
                    <td class="case_tableau_1">
                        <span class="db_quota_data">
                            <?php echo htmlspecialchars($row['value']);  ?>
                        </span>
                        <input class="db_quota_input" value="<?php echo htmlspecialchars($row['value']);  ?>" />
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
    </div>