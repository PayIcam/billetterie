<?php
require __DIR__ . '/general_requires/_header.php';


if(empty($_GET['ticket']))
{
    header('Location:'.$casUrl, true, 303);die();
}
else
{
    try
    {
        $Auth->loginUsingCas($_GET['ticket'], $_CONFIG['public_url']."login.php");
    }
    catch(Exception $e)
    {
        header('Location:'.$casUrl, true, 303);die();
    }

    try
    {
        $payutcClient->loginApp(array("key"=>$_CONFIG['payicam']['key']));
    }
    catch (\JsonClient\JsonException $e)
    {
        // $this->flash->addMessage('danger', "error login application");
    }
    $status = $payutcClient->getStatus();
    header('Location:'.$_CONFIG['base_path'].'', true, 303);
    die();
}

?>