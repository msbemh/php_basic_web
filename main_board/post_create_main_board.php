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
    $memo = $_POST["memo"];

    $base_dir = "/download/main_board_img/";
    $target_dir = "../download/main_board_img/";

    $remove_file_list = json_decode($_POST["remove_file_list"]);

    /**
     * 메인보드 데이터 Insert
     */
    $insert_id;
    $sql = "INSERT INTO main_board_tb(
                memo, 
                create_user, 
                create_date, 
                update_user, 
                update_date)
            VALUES ('$memo', '$email', now(), '$email', now())";
    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = $result_array['result'] && TRUE;
        $insert_id = $conn->insert_id;
        $result_array['main_board_id'] = $insert_id;
    } else {
        $result_array['result'] = $result_array['result'] && FALSE;
    }

    /**
     * 삭제 리스트에 있는 파일 삭제
     * 사용자 프로필 경로 null로 업데이트
     */
    // foreach ($remove_file_list as $key => $remove_file) {
    //     $remove_file = (array) $remove_file;
    //     $file_path = '..' . $remove_file['path'];
    //     $file_path = urldecode($file_path);
    //     $file_id = $remove_file;
    //     unlink("$file_path");

    //     $sql = "DELETE FROM main_board_file_tb
    //             WHERE id = '$file_id' ";
    //     if ($conn->query($sql) === TRUE) {
    //         $result_array['result'] = $result_array['result'] && TRUE;
    //     } else {
    //         $result_array['result'] = $result_array['result'] && FALSE;
    //     }
    // }

    /**
     * [파일 업로드]
     * basename(경로): 파일의 경로를 없앤 파일명만을 추출 합니다.
     * pathinfo(경로, 옵션) : 연관 배열을 반환니다
     *  - dirname: 파일의 디렉토리 경로를 나타냅니다.
     *  - basename: 파일 이름과 확장자를 나타냅니다.
     *  - extension: 파일의 확장자를 나타냅니다.
     *  - filename: 파일의 이름을 나타냅니다.
     * 
     * getimagesize(경로) : 이미지파일의 크기와 형식을 가져옵니다.
     */
    foreach ($_FILES as $key => $value) {
        // 현재 시간을 가져옵니다.
        $current_time = date('Ymd_His');

        $file = $value;
        $result = [];

        $uploadOk = 1;

        $file_info = pathinfo(basename($file["name"]));

        $imageFileType = $file_info['extension'];
        $filename = $file_info['filename'];

        // 파일에 현재시간을 추가하여 파일명을 중복되지 않게 합니다
        $result_filename = $filename . '_' . $current_time . '.' . $imageFileType;
        $target_file = $target_dir . $result_filename;

        // image가 진짜인지 가짜인지 판단
        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }

        // 이미 존재하는지 판단
        if (file_exists($target_file)) {
            $uploadOk = 0;
        }

        // 파일크기 체크
        if ($check["size"] > 500000) {
            $uploadOk = 0;
        }

        // 특정 포맷들만 통과
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $uploadOk = 0;
        }

        // 파일 업로드
        if ($uploadOk == 1) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $result['name'] = basename($file["name"]);
                $result['url'] = $base_dir . $result_filename;
                $result['size'] = $file["size"];

                $file_path = $result['url'];
                $sql = "INSERT INTO main_board_file_tb(
                            main_board_id, 
                            path,
                            create_user, 
                            create_date, 
                            update_user, 
                            update_date)
                        VALUES ('$insert_id', '$file_path', '$email', now(), '$email', now())";

                if ($conn->query($sql) === TRUE) {
                    $result_array['result'] = $result_array['result'] && TRUE;
                } else {
                    $result_array['result'] = $result_array['result'] && FALSE;
                }
            } else {
                $result_array['result'] = $result_array['result'] && FALSE;
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