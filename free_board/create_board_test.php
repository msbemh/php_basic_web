<?php
    // 에러 로그 출력
    require('../error_report.php');

    // DB Connection 얻어오기
    require('../db/db.php');

    try{
        $result_array = array();
        for ($i = 0; $i < 1000; $i++) {
            
            $sql = "INSERT INTO free_board_tb(title, content, views, create_user, create_date, update_user, update_date)
                    VALUES ('title$i', 'content$i', 0, 'admin', DATE_ADD(NOW(), INTERVAL $i SECOND), 'admin', DATE_ADD(NOW(), INTERVAL $i SECOND))";

            // 쿼리 실행
            $result = mysqli_query($conn, $sql);
            if($result === FALSE) $result_array['result'] = FALSE;
        }

        // 수동 commit
        mysqli_commit($conn);

        // json으로 변환하여 출력(Android로 보내자)
        echo json_encode($result_array);

        $conn->close();
    }catch(mysqli_sql_exception $exception){
        mysqli_rollback($conn);

        // 실패
        error_log ($exception, 3, "/usr/local/apache2/logs/php_error.log"); // 3: 파일 뒤쪽으로 이어서 작성하겠다는 의미
        $result_array['result'] = FALSE;
        echo json_encode($result_array);
    }
?>
