<?php
  $_CoreAuth->requireAuth();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>CoreAuth | Built-in Homepage</title>
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script type="text/javascript" src="static/js/coreauth.js"></script>
</head>
<body>
<h1>Welcome to CoreAuth</h1>
<p>You have been successfully authenticated!</p>
<p>To logout, please click <button onclick="deAuth()">this button.</button>.</p>
</body>
</html>