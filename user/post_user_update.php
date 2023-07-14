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
    $nick_name = $_POST["nick_name"];

    $base_dir = "/download/profile/";
    $target_dir = "../download/profile/";

    $remove_file_list = json_decode($_POST["remove_file_list"]);

    /**
     * 삭제 리스트에 있는 파일 삭제
     * 사용자 프로필 경로 null로 업데이트
     */
    foreach ($remove_file_list as $key => $remove_file) {
        $remove_file = (array) $remove_file;
        $file_path = '..' . $remove_file['path'];
        $file_path = urldecode($file_path);
        unlink("$file_path");

        $sql = "UPDATE user_tb SET
            profile_img = NULL
            WHERE email = '$email' ";
        if ($conn->query($sql) === TRUE) {
            $result_array['result'] = $result_array['result'] && TRUE;
            $_SESSION['profile_img'] = null;
        } else {
            $result_array['result'] = $result_array['result'] && FALSE;
        }
    }

    /**
     * 닉네임 변경
     */
    $sql = "UPDATE user_tb SET
            nick_name = '$nick_name'
            WHERE email = '$email' ";
    if ($conn->query($sql) === TRUE) {
        $result_array['result'] = $result_array['result'] && TRUE;
        $_SESSION['nick_name'] = $nick_name;
    } else {
        $result_array['result'] = $result_array['result'] && FALSE;
    }

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

                $profile_img = $result['url'];
                $sql = "UPDATE user_tb SET
                profile_img = '$profile_img'
                WHERE email = '$email' ";

                if ($conn->query($sql) === TRUE) {
                    $result_array['result'] = $result_array['result'] && TRUE;
                    $_SESSION['profile_img'] = $profile_img;
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