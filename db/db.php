<?php
    $host = "127.0.0.1";
    $username = "root";
    $password = "AlshalshSjrnf92@";
    $dbname = "basic_web_schema";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    // 자동 커밋 기능 끄기
    mysqli_autocommit($conn, FALSE);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>