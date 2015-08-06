<?php

// Database constants
// # Database Name
// $db_name = "postgres";
// # Database
// $db_host = "localhost";
// # DB port
// $db_port = "5432";
// # Database User
// $db_user = "postgres";
// 
// # Database password
// $db_pswd = "123psqluser";

// Connection
    $link = mysqli_connect('localhost:3306', 'root', '1229@Oxford', 'digisigres');
    if(!$link){
        die('Could not connect: ' . mysqli_error($link));
    }
    
    // $link = mysqli_connect('localhost:3306', 'digisig', '1EMeeIIINnn', 'digisigres');
    // if(!$link){
        // die('Could not connect: ' . mysqli_error($link));
    // }
?>