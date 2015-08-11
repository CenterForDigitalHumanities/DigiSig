<?php
    $_SESSION['userID'] = 1;
    $_SESSION['fk_access'] = 1;
    $_SESSION['fk_repository'] = 0;
    $_SESSION['user_email'] = "DigiSig";
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../DigiSig/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
    <p>You Have Successfully Logged Out.</p>
    <a onclick="window.location='index.php'">Return To Home</a>
</body>
</html>