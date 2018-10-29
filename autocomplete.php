<?php

require __DIR__ . '/general_requires/_header.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(!empty($_GET)) {
        isset($_GET['query']) ? $queryString = $_GET['query'] : $queryString = "";
        $result = $payutcClient->userAutocomplete(array("queryString" => $queryString));
        echo json_encode($result);
    } else {
        set_alert_style('Erreur routing');
        add_alert("Aucun paramètre n'a été transmis à la requête");
    }
} else {
    set_alert_style('Erreur routing');
    add_alert("Vous n'êtes pas censés appeler cette page directement.");
}
?>