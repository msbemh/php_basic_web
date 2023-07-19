<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {
    $result_array = array();

    // POST 파라미터 가져오기
    $email = $_POST["email"];
    $password = $_POST["password"];
    $nick_name = $_POST["nick_name"];
    $auth_num = $_POST["auth_num"];

    $email_trans = preg_replace("/[^A-Za-z0-9-]/", "", $email);

    // 인증번호 맞는지 확인
    if($_COOKIE[$email_trans] != $auth_num){
        $result_array['result'] = false;
        $result_array['msg'] = '인증번호가 맞지 않습니다.';
        echo json_encode($result_array);
        $conn->close();
        return;
    }

    // sha256 비밀번호 암호화
    $sha256_password = base64_encode(hash('sha256', $password, true));

    $sql = "INSERT INTO user_tb (
            email, 
            password, 
            nick_name, 
            create_user, 
            create_date, 
            update_user, 
            update_date)
            VALUES ('$email', '$sha256_password', '$nick_name', '$email', now(), '$email', now())";

    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = TRUE;
    } else {
        $result_array['result'] = FALSE;
    }

    // 수동 commit
    mysqli_commit($conn);

    // json으로 변환하여 출력(Android로 보내자)
    echo json_encode($result_array);

    $conn->close();
} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);

    // 실패
    error_log($exception, 3, "/var/log/apache2/php_error.log"); // 3: 파일 뒤쪽으로 이어서 작성하겠다는 의미
    $result_array['sresultuccess'] = FALSE;
    echo json_encode($result_array);
}
?>