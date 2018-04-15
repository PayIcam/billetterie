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
                            <td class="col-sm-4 <?=display_pourcentage_style($event_details_stats['pourcentage_inscriptions'], 1.8)?>"><?=$event_details_stats['pourcentage_inscriptions']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['total_quota']?></td>
                        </tr>
                        <?php if($event_details_stats['student_count'] !=0) { ?>
                        <tr>
                            <th class="col-sm-4">Etudiants Icam</th>
                            <td class="col-sm-4"><?=$event_details_stats['student_count']?></td>
                            <td class="col-sm-4 <?=display_pourcentage_style($event_details_stats['pourcentage_student'], 1.5)?>"><?=$event_details_stats['pourcentage_student']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['student_quota']?></td>
                        </tr>
                        <?php } if($event_details_stats['graduated_count'] !=0) { ?>
                        <tr>
                            <th class="col-sm-4">Ingénieurs Icam</th>
                            <td class="col-sm-4"><?=$event_details_stats['graduated_count']?></td>
                            <td class="col-sm-4 <?=display_pourcentage_style($event_details_stats['pourcentage_graduated'], 6)?>"><?=$event_details_stats['pourcentage_graduated']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['graduated_quota']?></td>
                        </tr>
                        <?php } if($event_details_stats['guests_count'] !=0) { ?>
                        <tr>
                            <th class="col-sm-4">Invités</th>
                            <td class="col-sm-4"><?=$event_details_stats['guests_count']?></td>
                            <td class="col-sm-4 <?=display_pourcentage_style($event_details_stats['pourcentage_guests'], 5)?>"><?=$event_details_stats['pourcentage_guests']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['guest_quota']?></td>
                        </tr>
                        <?php } ?>
                        <!-- <tr class="danger">
                            <th class="col-sm-4">Participants avec option</th>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                            <td class="col-sm-4">A venir</td>
                        </tr> -->
                        <?php if($event_details_stats['total_bracelet_count'] !=0) { ?>
                        <tr>
                            <th class="col-sm-4">Bracelets distribués</th>
                            <td class="col-sm-4"><?=$event_details_stats['total_bracelet_count']?></td>
                            <td class="col-sm-4 <?=display_pourcentage_style($event_details_stats['pourcentage_bracelets'], 1.8)?>"><?=$event_details_stats['pourcentage_bracelets']?></td>
                            <td class="col-sm-4"><?=$event_details_stats['total_count']?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-bordered">
                            <thead>
                                <th>Payement</th>
                                <th>Nombre</th>
                                <th>Pourcentage</th>
                            </thead>
                            <tbody>
                                <?php display_payments_stats($event_payment_stats, $total_count) ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-bordered">
                            <thead>
                                <th>Jour</th>
                                <th>Nombre</th>
                                <th>Pourcentage</th>
                            </thead>
                            <tbody>
                                <?php display_days_stats($event_days_stats, $total_count) ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Promotion</th>
                            <th>Nombre de participants</th>
                            <th>Pourcentage quota</th>
                            <th>Quota</th>
                            <th>Pourcentage évènement</th>
                            <th>Invités</th>
                            <th>Pourcentage invités</th>
                            <th>Bracelets</th>
                            <th>Pourcentage bracelets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        display_promo_stats($promo_specification_details_stats);
                        ?>
                    </tbody>
                </table>


            </section>
        </div>
    </body>
</html>