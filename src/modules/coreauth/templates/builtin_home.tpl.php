<?php
if(!$BoilerPlate->Modules["_CoreAuth"]->checkAuth()){
  header("Location:".$BoilerPlate->BASE_URL);
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>CoreAuth | Built-in Homepage</title>
</head>
<body>
<h1>Welcome to CoreAuth</h1>
<p>You have been successfully authenticated!</p>
<p>To logout, please click <button>this button.</button>.</p>
</body>
</html>