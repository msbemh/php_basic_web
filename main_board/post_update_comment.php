<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {
    $result_array = array();
    $result_array['result'] = true;

    // 세션 정보
    session_start();
    $email = $_SESSION['email'];

    // POST 파라미터 가져오기
    $comment = $_POST["comment"];
    $id = $_POST["id"];

    /**
     * 일반 댓글 Insert
     */
    $sql = "UPDATE main_board_comment_tb SET 
            comment = '$comment',
            update_user = '$email',
            update_date = now()
            WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = $result_array['result'] && TRUE;
    } else {
        $result_array['result'] = $result_array['result'] && FALSE;
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