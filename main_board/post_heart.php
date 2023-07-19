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
    // $main_board_id = $_POST["main_board_id"];
    $post_date = json_decode(file_get_contents( 'php://input' ), true);
    $main_board_id = $post_date["main_board_id"];
    
    $sql = "SELECT 
                count(id) as cnt  
            FROM main_board_heart_tb
            WHERE main_board_id = '$main_board_id'
            AND heart_supplier = '$email' ";

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

    // 좋아요 누르지 않았다면, insert
    if ($cnt == 0) {
        $sql = "INSERT INTO main_board_heart_tb (
            main_board_id,
            heart_supplier,
            create_user,
            create_date,
            update_user,
            update_date
        )
        VALUES (
            '$main_board_id',
            '$email',
            '$email',
            now(),
            '$email',
            now()
        )";
        $result_array['is_heart_exist'] = true;
    // 좋아요가 이미 있다면, delete
    } else {
        $sql = "DELETE FROM main_board_heart_tb 
                WHERE main_board_id = '$main_board_id'
                AND heart_supplier = '$email' ";
        $result_array['is_heart_exist'] = false;
    }

    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = $result_array['result'] && TRUE;
    } else {
        $result_array['result'] = $result_array['result'] && FALSE;
    }

    // 해당 게시글 전체 좋아요 개수
    $sql = "SELECT 
                count(id) as cnt  
            FROM main_board_heart_tb
            WHERE main_board_id = '$main_board_id' ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    $total_cnt = 0;
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $total_cnt = $r['cnt'];
    }
    $result_array['total_cnt'] = $total_cnt;

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