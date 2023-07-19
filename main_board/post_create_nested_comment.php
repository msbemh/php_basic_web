<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {
    $result_array = array();
    $result_array['result'] = true;
    $result_array['data'] = [];

    // 세션 정보
    session_start();
    $email = $_SESSION['email'];

    // POST 파라미터 가져오기
    $nested_comment = $_POST["nested_comment"];
    $main_board_id = $_POST["main_board_id"];
    $parent_id = $_POST["parent_id"];

    /**
     * 댓글 insert
     */
    $insert_id;

    if(empty($parent_id)){
        $sql = "INSERT INTO main_board_comment_tb (parent_id, main_board_id, comment, create_user, create_date, update_user, update_date)
        VALUES (null, '$main_board_id', '$nested_comment', '$email', now(), '$email', now())";
    }else{
        $sql = "INSERT INTO main_board_comment_tb (parent_id, main_board_id, comment, create_user, create_date, update_user, update_date)
        VALUES ('$parent_id', '$main_board_id', '$nested_comment', '$email', now(), '$email', now())";
    }
    

    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = $result_array['result'] && TRUE;
        $insert_id = $conn->insert_id;
    } else {
        $result_array['result'] = $result_array['result'] && FALSE;
    }

    /**
     * 게시글 정보 가져오기
     */
    $sql = "SELECT
            A.id
            ,A.parent_id
            ,A.comment
            ,A.create_user
            ,A.create_date
            ,B.nick_name
            ,B.profile_img as create_profile_img
        FROM main_board_comment_tb A
        LEFT OUTER JOIN user_tb B
        ON A.create_user = B.email
        WHERE A.id = $insert_id ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($result_array['data'], $r);
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