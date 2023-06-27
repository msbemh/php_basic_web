<?php
    echo "MySQL 연결 테스트<br>";

    error_reporting(E_ALL);
    
    ini_set("display_errors", 1);

    if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
        echo 'MySQLi is not installed!';
    } else {
        echo 'MySQLi is installed!';
    }


    $conn = mysqli_connect('127.0.0.1', 'root', 'AlshalshSjrnf92@');
    if (!$conn) {
        die('Could not connect to MySQL: ' . mysqli_connect_error());
    }
    echo 'Connected successfully';
    mysqli_close($conn);
?>