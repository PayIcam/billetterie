<!DOCTYPE html>
<html lang="fr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- Le styles -->
    <link href="<?= $RouteHelper->publicPath ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $RouteHelper->publicPath ?>css/main.css" rel="stylesheet">
    <meta name="description" content="Shotgun">
    <meta name="author" content="Thibaut de Gouberville 118, Guillaume Dubois 119, Gregoire Dervaux 119">
    <link rel="stylesheet" href="<?= $RouteHelper->publicPath ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="<?= $RouteHelper->publicPath ?>img/icone.png">
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <title><?= $RouteHelper->getPageTitle() ?></title>
  </head>

  <body>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Payicam</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li><a class="nav-link" href="<?= $RouteHelper->getPathFor() ?>">Home</a></li>
            <li><a class="nav-link" href="<?= $RouteHelper->getPathFor('about') ?>">About</a></li>
            <li><a class="nav-link" href="<?= $RouteHelper->getPathFor('logout') ?>">Déconnexion</a></li>
          </ul>
        </div>
      </nav>
          
    <?php foreach ($flash->getMessages() as $key => $flashs): ?>
      <?php foreach ($flashs as $flashMsg): ?>
        <div class="grey alert-<?= $key ?>"><a onclick="location.reload()">X</a><?php echo $flashMsg ?></div>
      <?php endforeach ?>
    <?php endforeach ?>