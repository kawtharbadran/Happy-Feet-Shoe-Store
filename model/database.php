<?php

    $dsn = 'mysql:host=localhost;dbname=happyfeetshoestore';
    $username = 'hpss_user';
    $password = '!fan5ysh00z';

    try {
        $db = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('database_error.php');
        exit();
    }
?>
