<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {

    // POST 파라미터 가져오기
    $title = $_POST["title"];
    $content = $_POST["content"];
    $file_path_infos = $_POST["file_path_infos"];

    // 추가된 데이터의 id
    $insert_id;

    $sql = "INSERT INTO free_board_tb(
                    title, 
                    content, 
                    views, 
                    create_user, 
                    create_date, 
                    update_user, 
                    update_date)
                VALUES ('$title', '$content', 0, 'admin', now(), 'admin', now())";

    // free_board_tb 데이터 추가
    $result_array = array();
    if ($conn->query($sql) === TRUE) {
        $insert_id = $conn->insert_id;

        $result_array['result'] = TRUE;
        $result_array['id'] = $insert_id;
    } else {
        $result_array['result'] = FALSE;
    }

    // 추가한 파일이 있을 경우 DB에 경로 추가
    if (!empty($file_path_infos) && count($file_path_infos) > 0) {
        foreach ($file_path_infos as $file_path_info) {
            $file_path = $file_path_info['path'];
            $file_path = urldecode($file_path);
            $type = $file_path_info['type'];

            $sql = "INSERT INTO free_board_file_tb(
                    free_board_id, 
                    path, 
                    type,
                    create_user, 
                    create_date, 
                    update_user, 
                    update_date)
                VALUES ('$insert_id', '$file_path', '$type', 'admin', now(), 'admin', now())";

            // free_board_file_tb 데이터 추가
            if ($conn->query($sql) === TRUE) {
                $result_array['result'] = TRUE;
            } else {
                $result_array['result'] = FALSE;
            }
        }
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