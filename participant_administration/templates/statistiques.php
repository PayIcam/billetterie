<!-- Template des statistiques relatives à un évènement -->

<?php set_header_navbar('Statistiques : ' . htmlspecialchars($event_details_stats['name']))?>
        <div class="container" style="width: 80%">
            <h1 class="text-center">Statistiques :  <?= htmlspecialchars($event_details_stats['name'])?></h1>

            <?php display_back_to_list_button($event_id); ?>
            <?php echo '<br>'; display_go_to_arrivals($event_id); ?>

            <div class="container" style="width: 80%; margin-top: 25px">
                <h2 class="text-center">Généralités sur l'évènement</h2>
                <div class="row">
                    <div class="col-sm-9">
                        <table class="table table-bordered">
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
                                    <th class="col-sm-3">Tous les participants</th>
                                    <td class="col-sm-3"><?=$event_details_stats['total_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_inscriptions'], 2)?>"><?=$event_details_stats['pourcentage_inscriptions']?></td>
                                    <td class="col-sm-3"><?=$event_details_stats['total_quota']?></td>
                                </tr>
                                <?php if($event_details_stats['student_count'] !=0 && !($event_details_stats['graduated_count']==0 && $event_details_stats['guests_count']==0)) { ?>
                                <tr>
                                    <th class="col-sm-3">Etudiants Icam</th>
                                    <td class="col-sm-3"><?=$event_details_stats['student_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_student'], 2)?>"><?=$event_details_stats['pourcentage_student']?></td>
                                    <td class="col-sm-3"><?=$event_details_stats['student_quota']?></td>
                                </tr>
                                <?php } if($event_details_stats['graduated_count'] !=0) { ?>
                                <tr>
                                    <th class="col-sm-3">Ingénieurs Icam</th>
                                    <td class="col-sm-3"><?=$event_details_stats['graduated_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_graduated'], 6)?>"><?=$event_details_stats['pourcentage_graduated']?></td>
                                    <td class="col-sm-3"><?=$event_details_stats['graduated_quota']?></td>
                                </tr>
                                <?php } if($event_details_stats['guests_count'] !=0) { ?>
                                <tr>
                                    <th class="col-sm-3">Invités</th>
                                    <td class="col-sm-3"><?=$event_details_stats['guests_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_guests'], 2)?>"><?=$event_details_stats['pourcentage_guests']?></td>
                                    <td class="col-sm-3"><?=$event_details_stats['guest_quota']?></td>
                                </tr>
                                <?php } if($event_details_stats['options_count'] !=0) { ?>
                                <tr>
                                    <th class="col-sm-3">Participants ayant pris des options</th>
                                    <td class="col-sm-3"><?=$event_details_stats['options_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_options'], 2)?>"><?=$event_details_stats['pourcentage_options']?></td>
                                    <td class="col-sm-3">XXX</td>
                                </tr>
                                <?php } if($event_details_stats['total_bracelet_count'] !=0) { ?>
                                <tr>
                                    <th class="col-sm-3">Bracelets distribués</th>
                                    <td class="col-sm-3"><?=$event_details_stats['total_bracelet_count']?></td>
                                    <td class="col-sm-3 <?=display_pourcentage_style($event_details_stats['pourcentage_bracelets'], 2)?>"><?=$event_details_stats['pourcentage_bracelets']?></td>
                                    <td class="col-sm-3"><?=$event_details_stats['total_count']?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-3">
                        <table class="table table-bordered col-sm-3">
                            <thead>
                                <tr>
                                    <th>Entrées</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?=$event_details_stats['arrival_count']?></td>
                                    <td class="<?=display_pourcentage_style($event_details_stats['pourcentage_arrival'], 2)?>"><?=$event_details_stats['pourcentage_arrival']?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="row">
                    <h2 class="col-sm-6 text-center">Moyens de payements</h2>
                    <h2 class="col-sm-6 text-center">Inscriptions sur les 7 derniers jours :</h2>
                </div>
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
            </div>

            <h2 class="text-center">Promotions</h2>
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
                    <?php display_promo_stats($promo_specification_details_stats); ?>
                </tbody>
            </table>

            <h2 class="text-center">Options</h2>
            <div id="options">
                <div class="row">
                    <?php display_option_stats($options_stats) ?>
                </div>
            </div>

        </div>
    </body>
</html>