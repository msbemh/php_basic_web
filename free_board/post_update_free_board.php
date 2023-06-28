<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {

    // POST 파라미터 가져오기
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $file_path_infos = $_POST["file_path_infos"];
    // 이걸로 기존에 있던 path와 새롭게 들어온 path를 구별하여, 새롭게 들어온 path만 DB에 경로 추가해준다.
    $file_path_infos_copy = [];
    if (!empty($file_path_infos) && count($file_path_infos) > 0) {
        $file_path_infos_copy = array_replace([], $file_path_infos);
    }

    $sql = "UPDATE free_board_tb SET 
                title = '$title',
                content = '$content',
                update_user = 'admin',
                update_date = now()
            WHERE id = $id";

    // free_board_tb 데이터 수정
    $result_array = array();
    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = TRUE;
    } else {
        $result_array['result'] = FALSE;
    }

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
        $db_file_type = $r['type'];

        /**
         * DB에 있는 파일 경로중 file_path_infos에 없는 경로의 경우
         * 실제 파일을 삭제시키고, DB도 삭제시키자
         */
        $base_dir = '';

        // db에 저장된 이미지 경로
        $db_file_path = $r['path'];
        $file_full_path = '..' . $r['path'];

        // db에는 이미지 경로가 존재하지만, 파라미터로 넘겨받은 이미지 경로에는 없다면 삭제 시킨다.
        $is_exist = FALSE;
        foreach ($file_path_infos as $file_info) {
            $file_path = $file_info['path'];
            $file_path = urldecode($file_path);
            if ($db_file_path == $file_path) {
                $is_exist = TRUE;
                // 밑에서 남은 file_path들은 새롭게 추가하기 위해서
                $key = array_search($file_info, $file_path_infos_copy);
                if ($key !== false) {
                    unset($file_path_infos_copy[$key]);
                }
            }
        }

        if (!$is_exist) {
            // 실제 파일 삭제
            unlink("$file_full_path");

            // DB 파일 경로 삭제
            $sql = "DELETE FROM free_board_file_tb
                    WHERE path = '$db_file_path' ";

            // 쿼리 실행
            if ($conn->query($sql) === TRUE) {
                $result_array['result'] = TRUE;
            } else {
                $result_array['result'] = FALSE;
            }
        }
    }

    // 새로운 파일경로의 경우 DB에 추가
    // 추가한 파일이 있을 경우 DB에 경로 추가
    if (count($file_path_infos_copy) > 0) {
        foreach ($file_path_infos_copy as $file_info) {
            $file_path = $file_info['path'];
            $file_path = urldecode($file_path);
            $sql = "INSERT INTO free_board_file_tb(
                    free_board_id, 
                    path, 
                    create_user, 
                    create_date, 
                    update_user, 
                    update_date)
                VALUES ('$id', '$file_path', 'admin', now(), 'admin', now())";

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