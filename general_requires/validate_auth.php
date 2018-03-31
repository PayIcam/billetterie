<?php

if(!in_array($route, ['login.php', 'callback.php']))
{
    if((!isset($status) || !$status->user))// Il n'était pas encore connecté en tant qu'icam.
    {
        // $this->flash->addMessage('info', "Vous devez être connecté pour accéder au reste de l'application");
        if($_SERVER['REQUEST_URI'] != '/billetterie/')
        {
            $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        }
        header('Location:'.$casUrl, true, 303);
        die();
    }
    if (!empty($status->user))
    {
        if (empty($status->application) || isset($status->application->app_url) && strpos($status->application->app_url, 'billetterie') === false)// il était connecté en tant qu'icam mais l'appli non
        {
            if($_SERVER['REQUEST_URI'] != '/billetterie/')
            {
                $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            }
            try
            {
                $payutcClient->loginApp(array("key"=>$_CONFIG['payicam']['key']));
                $status = $payutcClient->getStatus();
            }
            catch (\JsonClient\JsonException $e)
            {
                // $this->flash->addMessage('info', "error login application, veuillez finir l'installation de l'app");
                header('Location:'.$casUrl, true, 303);die();
            }
        }
        // tout va bien
        $icam_informations = json_decode(file_get_contents($_CONFIG['ginger']['url'].$Auth->getUserField('email')."/?key=".$_CONFIG['ginger']['key']));
        $icam_informations->promo_id = get_promo_id($icam_informations->promo);
        $icam_informations->site_id = get_site_id($icam_informations->site);
        if(empty($icam_informations))// l'utilisateur n'avait jamais été ajouté à Ginger O.o
        {
            $icam_informations = json_decode(file_get_contents($_CONFIG['ginger']['url'].$Auth->getUserField('email')."/?key=".$_CONFIG['ginger']['key']));
        }
        $_SESSION['icam_informations'] = $icam_informations;

    }
    if (empty($icam_informations))// l'utilisateur n'a pas un mail icam valide // on ne devrait jamais avoir cette erreur car on passe par payutc et lui a besoin d'avoir ginger qui marche ...
    {
        // $this->flash->addMessage('warning', "Votre Mail Icam n'est pas reconnu par Ginger...");
        header('Location:'.$casUrl, true, 303);die();
    }
}
