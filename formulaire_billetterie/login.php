<?php
require __DIR__ . '/general_requires/_header.php';

if(empty($_GET['ticket'])) {
    // header('Location:'.$casUrl, true, 303);die();
} else {
    try {
        $result = $payutcClient->loginCas(array("ticket" => $_GET['ticket'], "service" => $_CONFIG['public_url']."login.php"));
        $status = $payutcClient->getStatus();

        $_SESSION['payutc_cookie'] = $payutcClient->cookie;
        $userRank = $payutcClient->getUserLevel();
        $role = $Auth->getRole($userRank);

        $_SESSION['Auth'] = array(
            'email' => $status->user,
            'firstname' => $status->user_data->firstname,
            'lastname' => $status->user_data->lastname,
            'slug' => $role['slug'],
            'roleName' => $role['name'],
            'level' => $userRank
        );
    } catch (Exception $e) {
        if (strpos($e, 'UserNotFound') !== false ) {
            $this->flash->addMessage('info', 'Vous ne faites pas encore parti de PayIcam, inscrivez vous.');
            header('Location:'.basename(basename($_CONFIG['payicam']['url'])).'/casper', true, 303);die();
        }
    }
    try {
        $result = $payutcClient->loginApp(array("key"=>$_CONFIG['payicam']['key']));
    } catch (\JsonClient\JsonException $e) {
        $this->flash->addMessage('danger', "error login application");
   }
    $status = $payutcClient->getStatus();
    header('Location:'.$_CONFIG['base_path'].'', true, 303);die();

}

?>

<h1>Connexion</h1>
<p>
    <?php if ($Auth->isLogged()){ ?>
        <a href="<?= $logoutUrl ?>" class="btn btn-default">déconnexion</a>
    <?php } else { ?>
        <a href="<?= $casUrl ?>" class="btn btn-primary">Connectez-vous !</a>
    <?php } ?>
</p>