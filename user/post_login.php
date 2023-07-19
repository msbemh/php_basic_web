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

    // sha256 비밀번호 암호화
    $sha256_password = base64_encode(hash('sha256', $password, true));

    $sql = "SELECT 
                email, 
                password, 
                nick_name,
                profile_img
            FROM user_tb
            WHERE email = '$email'
            AND password= '$sha256_password'";

    $result = mysqli_query($conn, $sql);
    $rows = [];

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($rows, $r);
    }

    // 데이터가 있다면 로그인 성공
    if (count($rows) > 0) {
        $result_obj['result'] = true;
        /**
         * [세션 시작]
         */
        session_start();
        $_SESSION["email"] = $rows[0]['email'];
        $_SESSION["nick_name"] = $rows[0]['nick_name'];
        $_SESSION["profile_img"] = $rows[0]['profile_img'];

        //세션파괴
        // remove all session variables
        // session_unset();
        // destroy the session
        // session_destroy();

    } else {
        $result_obj['result'] = false;
    }

    // json으로 변환하여 출력(Android로 보내자)
    echo json_encode($result_obj);

    $conn->close();
} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);

    // 실패
    error_log($exception, 3, "/var/log/apache2/php_error.log"); // 3: 파일 뒤쪽으로 이어서 작성하겠다는 의미
    $result_obj['sresultuccess'] = FALSE;
    echo json_encode($result_obj);
}
?>