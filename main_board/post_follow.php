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
    // $opponent_email = $_POST['opponent_email'];

    $post_date = json_decode(file_get_contents( 'php://input' ), true);
    $opponent_email = $post_date["opponent_email"];

    $sql = "SELECT 
                count(id) as cnt  
            FROM main_board_follow_tb
            WHERE follower = '$email'
            AND followed = '$opponent_email' ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    $cnt = 0;
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $cnt = $r['cnt'];
    }

    // 팔로우 누르지 않았다면, insert
    if ($cnt == 0) {
        $sql = "INSERT INTO main_board_follow_tb (
                    follower,
                    followed,
                    create_user,
                    create_date,
                    update_user,
                    update_date
                ) VALUES (
                    '$email',
                    '$opponent_email',
                    '$email',
                    now(),
                    '$email',
                    now()
                )";
        $result_array['is_followed'] = true;
    // 팔로우가 이미 있다면, delete
    } else {
        $sql = "DELETE FROM main_board_follow_tb 
                WHERE follower = '$email'
                AND followed = '$opponent_email' ";
        $result_array['is_followed'] = false;
    }

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