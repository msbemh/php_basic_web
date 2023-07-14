<?php

$result_array = array();

try {
    session_start();

    // remove all session variables
    session_unset();

    // destroy the session
    session_destroy();

    $result_array['result'] = true;
} catch (mysqli_sql_exception $exception) {
    // 실패
    error_log($exception, 3, "/var/log/apache2/php_error.log"); // 3: 파일 뒤쪽으로 이어서 작성하겠다는 의미
    $result_array['result'] = false;
}

echo json_encode($result_array);
?>