<?php
    include "../config/config.php";
    
    if(isset($_POST['user_email']) && isset($_POST['password'])){
        $email = $_POST['user_email'];
        $pwd = $_POST['password'];
        $login = "select * from user_digisig where user_email = '".$email."' and password='".$pwd."'";

        $queryresults = mysqli_query($link, $login);
        $count = mysqli_num_rows($queryresults);
        if($count > 0){
            $row = mysqli_fetch_array($queryresults);
            $_SESSION['userID'] = $row['pk_user'];
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION['fk_access'] = $row['fk_access'];
            $_SESSION['fk_repository'] = $row['fk_repository'];
        }
        else{
            echo 'User email or password error, cannot log in. ';
        }
    }
?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../DigiSig/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
    <div class="container">
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
