<?php
    #connection details
    # Database Name
    $db_name = "postgres";
    # Database
    $db_host = "localhost";
    # DB port
    $db_port = "5432";
    # Database User
    $db_user = "admin";

    # Database password
    $db_pswd = "123psqluser";

    # Connection
    $pg = pg_connect("host=$db_host port=$db_port user=$db_user password=$db_pswd dbname=$db_name")
            or die ("Where-o-where has the database gone?");
    
    if(isset($_POST['user_email']) && isset($_POST['password'])){
        $email = $_POST['user_email'];
        $pwd = $_POST['password'];
        $login = "select * from user_digisig where user_email = '".$email."' and password='".$pwd."'";

        $queryresults = pg_query($login);
        $count = pg_num_rows($queryresults);
        if($count > 0){
            $row = pg_fetch_array($queryresults);
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
    <link rel="stylesheet" href="http://localhost:8080/DigiSig/include/lightbox/css/lightbox.css">
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