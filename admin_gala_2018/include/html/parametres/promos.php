    <div class="stats_promos">
        <div class="container_tableau3">
            <legend style="text-align:center;font-size:2em; margin: 30px 0px"> Statistiques par Promo </legend>
            <table class="table table-bordered tableau3" style="background-color: #ffffff;">
                <thead>
                    <tr class="ligne_tableau3">
                        <th class="case_tableau2"> Promo </th>
                        <th class="case_tableau3"> Inscris </th>
                        <th class="case_tableau3"> Invités </th>
                        <th class="case_tableau3"> Pourcentage d'inscris </th>
                        <th class="case_tableau3"> Total promo </th>
                        <th class="case_tableau3"> Promo bracelets </th>
                        <th class="case_tableau3"> Invités bracelets </th>
                        <th class="case_tableau3"> Pourcentage de bracelets </th>
                    </tr>
                </thead>
                <tbody>
                <?php
                for ($i=10; $i<count($status); $i++)
                {
                    $row = $status[$i];
                ?>
                    <tr class="ligne_tableau3">
                        <td class="case_tableau2"><?php echo htmlspecialchars($row['Promo']);  ?> </td>
                        <td class="case_tableau3"><?php echo htmlspecialchars($row['inscris']);  ?> </td>
                        <td class="case_tableau3"><?php echo htmlspecialchars($row['invite']);  ?> </td>
                        <td class="case_tableau3"><?php echo color_percentage(($row['pourcentage_inscris']));  ?> </td>
                        <td class="case_tableau3"><?php echo htmlspecialchars($row['total_promo']);  ?> </td>
                        <td class="case_tableau3"><?php echo htmlspecialchars($row['bracelets_promo']);  ?> </td>
                        <td class="case_tableau3"><?php echo htmlspecialchars($row['bracelets_invite']);  ?> </td>
                        <td class="case_tableau3"><?php echo color_percentage(($row['pourcentage_bracelets']));  ?> </td>

                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
