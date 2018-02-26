    <div style="margin:50px 0px;" class="container_comparaisons">
    <?php
    for ($i=6; $i<=9; $i++)
    {
        $row = $status[$i];
    ?>
        <div class="container_tableau2">
            <legend><?php echo htmlspecialchars($row['name']); ?></legend>
            <table class="table table-bordered tableau2" style="background-color: #ffffff;">
                <thead>
                    <tr class="ligne_tableau2">
                        <th class="case_tableau2"><?php echo htmlspecialchars($row['name1']); ?></th>
                        <th class="case_tableau2"><?php echo htmlspecialchars($row['name2']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="ligne_tableau2">
                        <td class="case_tableau2"><?php echo htmlspecialchars($row['value1']);  ?> </td>
                        <td class="case_tableau2"><?php echo htmlspecialchars($row['value2']);  ?> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php
    }
    ?>
    </div>
