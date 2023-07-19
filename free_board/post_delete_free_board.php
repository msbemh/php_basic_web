<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {

    // POST 파라미터 가져오기
    $id = $_POST["id"];

    /**
     * file 테이블에서 해당 게시글과 관련된 이미지 파일들을 모두 삭제시키자
     */
    $sql = "SELECT
            path,
            type
        FROM free_board_file_tb 
        WHERE free_board_id = $id ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $file_path = '..' . $r['path'];
        $file_path = urldecode($file_path);
        unlink("$file_path");
    }

    $sql = "DELETE FROM free_board_tb
            WHERE id = $id";

    // 쿼리 실행
    $result_array = array();
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