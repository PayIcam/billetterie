<?php set_header_navbar('Statistiques : ' . htmlspecialchars($event_details_stats['name']))?>
        <div class="container">
            <h1 class="text-center">Statistiques :  <?= htmlspecialchars($event_details_stats['name'])?></h1>

            <?php display_back_to_list_button($event_id); ?>

            <section class="row" id="tableau">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quota actuel</th>
                            <th>Pourcentage</th>
                            <th>Quota maximal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="col-sm-4">Tous les participants</th>
                            <td class="col-sm-4"><?=$event_details_stats['total_count']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['pourcentage_inscriptions']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['total_quota']?></td>
                        </tr>
                        <tr>
                            <th class="col-sm-4">Bracelets distribués</th>
                            <td class="col-sm-4"><?=$event_details_stats['total_bracelet_count']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['pourcentage_bracelets']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['total_count']?></td>
                        </tr>
                        <tr class="danger">
                            <th class="col-sm-4">Etudiants Icam</th>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                        </tr>
                        <tr class="danger">
                            <th class="col-sm-4">Ingénieurs Icam</th>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                        </tr>
                        <tr class="danger">
                            <th class="col-sm-4">Participants avec option</th>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Promotion</th>
                            <th>Nombre de participants</th>
                            <th>Pourcentage quota</th>
                            <th>Quota</th>
                            <th>Pourcentage évènement</th>
                            <th>Bracelets</th>
                            <th>Pourcentage bracelets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        display_promo_stats($promo_specification_details_stats, $total_quota);
                        ?>
                    </tbody>
                </table>


            </section>
        </div>
    </body>
</html>