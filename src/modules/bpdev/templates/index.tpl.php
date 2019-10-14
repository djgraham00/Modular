<?php 

if($BoilerPlate->hasMod("coreauth"))
{
    if($_CoreAuth->enableLoginRedir)
    {
        header("Location: ./login");
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
<h1>Welcome to Boilerplate</h1>
<p>This sample application was created to showcase the basic usage of Boilerpate.</p>

<?php
if($BoilerPlate->hasMod("coreauth")){
?>
<p>Follow this <a href="login/">link</a> to visit the CoreAuth login page.</p>
<?php
}
?>

</body>
</html>
