<?php

// Database constants

# Database
$db_host = "localhost";

# Database User
$db_user = "postgres";

# Database password
$db_pswd = "letmein";

# Database Name
$db_name = "digitalsigillographyresource";

# Connection
$pg = pg_connect("host=$db_host user=$db_user password=$db_pswd dbname=$db_name")
        or die ("Where-o-where has the database gone?");

        
?>