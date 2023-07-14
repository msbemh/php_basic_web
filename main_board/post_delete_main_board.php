<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {
    $result_array = array();
    $result_array['result'] = true;

    $file_array = array();

    // 세션 정보
    session_start();
    $email = $_SESSION['email'];

    // POST 파라미터 가져오기
    $main_board_id = $_POST["main_board_id"];

    /**
     * 관련 파일 목록 검색
     */
    $sql = "SELECT
            id,
            path
        FROM main_board_file_tb
        WHERE main_board_id = '$main_board_id' ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($file_array, $r);
    }

    /**
     * 파일 실제 삭제
     */
    foreach ($file_array as $key => $remove_file) {
        $remove_file = (array) $remove_file;
        $file_path = '..' . $remove_file['path'];
        $file_id = $remove_file['id'];
        $file_path = urldecode($file_path);
        unlink("$file_path");

        $sql = "DELETE FROM main_board_file_tb
                WHERE id = '$file_id' ";
        if ($conn->query($sql) === TRUE) {
            $result_array['result'] = $result_array['result'] && TRUE;
        } else {
            $result_array['result'] = $result_array['result'] && FALSE;
        }
    }


    /**
     * 게시글 삭제, 포린키로 나머지도 자동 삭제
     */
    $sql = "DELETE FROM main_board_tb
            WHERE id = '$main_board_id'";
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