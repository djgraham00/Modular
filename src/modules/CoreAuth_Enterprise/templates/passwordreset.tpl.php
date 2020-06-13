<?php

?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?= $ModularPHP->APP_NAME; ?> > Reset Password</title>
    <base href="<?= $ModularPHP->APP_BASE_URL ?>" target="_blank">
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script type="text/javascript" src="static/js/coreauth.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="static/css/bootstrap-login.css">
</head>
<body class="text-center">
<form class="form-signin" method="post" onsubmit='login("<?= $_CoreAuth->Config->LoginRdir; ?>");return false;'>
    <img class="mb-4" src="static/img/perkinstryon.png" alt="" width="172"><br/>
    <!-- <h1 class="h3 mb-3 font-weight-normal"> //$ModularPHP->APP_NAME; </h1> -->
    <span style="color: red; font-weight:bold" id="err">Resetting passwords is currently disabled. Please contact an administrator.</span>
    <!-- <label for="inputUsername" class="sr-only">Username</label>
    <input type="text" id="username" name="username"  class="form-control" placeholder="Username" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>-->
    <p class="mt-5 mb-3 text-muted">Perkins-Tryon Public Schools <br/>Information Technology Department</p>
</form>
</body>
</html>