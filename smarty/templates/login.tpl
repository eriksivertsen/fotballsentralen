<head>
    <meta charset="utf-8">
    <title>MatchObserver Login</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css" />
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="http://www.modernizr.com/downloads/modernizr-latest.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>
</head>
<?php session_start(); ?>
<body>
    <form id="slick-login" action="admin.php" method="post">
        <label for="username">username</label><input id="username" type="text" name="username" class="placeholder" placeholder="e-post">
        <label for="password">password</label><input id="password" type="password" name="password" class="placeholder" placeholder="passord">
        <input type="submit" value="Logg inn"/>
    </form>
</body>
</html>