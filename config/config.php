<?php

# Connection
	#connection for SLU server
    $link = mysqli_connect('localhost:3306', 'digisig', '1EMeeIIINnn', 'digisigres'); //img01
	#connection for local server
    #$link = mysqli_connect('localhost:3306', 'digisig_user', 'password', 'digisig_local'); //john
    if(!$link){
        die('Could not connect: ' . mysqli_error($link));
    }

    ?>