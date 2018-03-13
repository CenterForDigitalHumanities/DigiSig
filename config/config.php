<?php

# Connection
	#connection for local cubap
    $link = mysqli_connect('localhost:3306', 'root', '', 'digisig');
    if(!$link){
        die('Could not connect: ' . mysqli_error($link));
    }

    ?>