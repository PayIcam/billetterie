    <div class="stats_promos">
        <div class="container_tableau4">
            <legend style="text-align:center;font-size:2em; margin: 30px 0px"> Créneaux des 7 derniers jours </legend>
            <table class="table table-bordered tableau4" style="background-color: #ffffff;">
                <thead>
                    <tr class="ligne_tableau4">
                        <th class="case_tableau4"> Date </th>
                        <th class="case_tableau4"> 1er Créneau </th>
                        <th class="case_tableau4"> 2e Créneau </th>
                        <th class="case_tableau4"> 3e Créneau </th>
                        <th class="case_tableau4"> Total Ajouts </th>
                    </tr>

                </thead>
                <tbody>
                <?php
                for ($i=0; $i<=6; $i++)
                {
                    $row = $daily_stats[$i];
                ?>
                    <tr class="ligne_tableau4">
                        <td class="case_tableau4"><?php echo htmlspecialchars($row['date']);  ?> </td>
                        <?php
                        for($j=0; $j<=3; $j++)
                        {
                        ?>
                        <td class="case_tableau4"><?php echo htmlspecialchars($row[$j]['COUNT(*)']);  ?> </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>