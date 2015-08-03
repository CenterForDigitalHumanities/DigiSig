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
//$pg = pg_connect("host=$db_host port=$db_port user=$db_user password=$db_pswd dbname=$db_name")
//        or die ("Where-o-where has the database gone?");

$conn = pg_connect("host=$db_host user=$db_user password=$db_pswd dbname=$db_name")
        or die ("Where-o-where has the database gone?");
        
?>