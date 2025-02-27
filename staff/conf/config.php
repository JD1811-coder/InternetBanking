<?php
    $dbuser="root";
    $dbpass="";
    $host="localhost";
    $db="internetbanking";
    $mysqli=new mysqli($host,$dbuser, $dbpass, $db);
    // Add admin level to session
$_SESSION['admin_level'] = 'staff'; // or 'admin' based on your user roles
