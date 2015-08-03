<?php

// Database constants
# Database Name
$db_name = "postgres";
# Database
$db_host = "localhost";
# DB port
$db_port = "5432";
# Database User
$db_user = "postgres";

# Database password
$db_pswd = "123psqluser";

# Connection
<<<<<<< HEAD
    $link = mysqli_connect('localhost:3306', 'root', '1229@Oxford', 'digisigres');  
    if(!$link){
        die('Could not connect: ' . mysqli_error($link));
    }
=======
//$pg = pg_connect("host=$db_host port=$db_port user=$db_user password=$db_pswd dbname=$db_name")
//        or die ("Where-o-where has the database gone?");

$conn = pg_connect("host=$db_host user=$db_user password=$db_pswd dbname=$db_name")
        or die ("Where-o-where has the database gone?");
        
>>>>>>> a7d6a042793ee2d104e2720c9cd6de085cf45744
?>