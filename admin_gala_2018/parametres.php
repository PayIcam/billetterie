<?php

/**
 *
 * Cette page propose des statistiques sur les inscriptions du gala. Il y a 4 grandes parties.
 *
 * Tout d'abord, des statistiques sur les quotas, ensuite, des statistiques sur la distribution de bracelets.
 * Viennent ensuite des stats par promo, puis des stats sur les nouvelles inscriptions des 7 derniers jours.
 *
 */
require 'config.php';
require 'include/html/header.php';
require 'include/db_functions.php';
require 'include/display_functions.php';

$bd = connect_to_db($confSQL);

$status = set_quotas();
$daily_stats = set_creneaux_date();

require 'include/html/parametres/quotas.php';
require 'include/html/parametres/comparaisons.php';
require 'include/html/parametres/promos.php';
require 'include/html/parametres/day.php';
?>
<script src='js/change_quota_promo.js'> </script>
<?php
include 'include/html/footer.php'; ?>