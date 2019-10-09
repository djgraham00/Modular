<!DOCTYPE HTML>
<html>
<head>
    <title><?= $BoilerPlate->AppName; ?></title>
    <base href="<?= $BoilerPlate->BASE_URL ?>" target="_blank">
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script src="static/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="static/css/bootstrap-login.css">
</head>
<body class="text-center">
<form class="form-signin" method="post" onsubmit="login();return false;">
<h1 class="h3 mb-3 font-weight-normal"><?= $BoilerPlate->AppName; ?></h1>
    <span style="color: red; font-weight:bold" id="err"></span>
    <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="username" name="username"  class="form-control" placeholder="Username" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    <p class="mt-5 mb-3 text-muted">Powered by CoreAuth</p>
</form>

<script type="text/javascript">

function login() {

    let data = { username: document.getElementById("username").vale












        password: document.getElementById("password").value};

    console.log(JSON.stringify(data));

    fetch("./_coreAuthAPI/auth", {
        method: "POST",
        body: JSON.stringify(data)
    }).then(res => res.json()).then(data => {
        console.log(data);
        if(data === true) {
            window.location.replace ("./coreAuthHome");
        }
        else{
            document.getElementById("err").innerHTML = "Invalid Username or Password";
        }
    });

   }
</script>
</body>
</html>