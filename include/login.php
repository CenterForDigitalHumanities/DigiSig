<?php
    include "../config/config.php";
    
    
?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../digisig/include/lightbox/css/lightbox.css">
    <?php echo "<title>Log In</title>" ?>
</head>

<body>
    <div class="container">
        <h2 style="border-bottom: 1px solid black; width: 100%;">Log In</h2><br><br>
        <form action="../index.php" method="post">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="user_email" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="pwd">Password</label>
                <input type="password" class="form-control" id="pwd" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</body>
</html>
