<?php
header('Content-Type: application/json; charset=utf-8');

// 에러 로그 출력
require('../error_report.php');

// DB Connection 얻어오기
require('../db/db.php');

try {

    $result_array = array();
    $result_array['result'] = [
        // [
        //     "url" => "/download/editorImg/test_image.jpg",
        //     "name" => "test_image.jpg",
        //     "size" => "561276"
        // ]
    ];

    $base_dir = "/download/editor_video/";
    $target_dir = "../download/editor_video/";

    /**
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

        $result_filename = $filename . '_' . $current_time . '.' . $imageFileType;
        $target_file = $target_dir . $result_filename;

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
            $imageFileType != "mp4"
        ) {
            $uploadOk = 0;
        }

        // 파일 업로드
        if ($uploadOk == 1) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $result['name'] = basename($file["name"]);
                $result['url'] = $base_dir . $result_filename;
                $result['size'] = $file["size"];

                array_push($result_array['result'], $result);
            } else {
                echo "파일 업로드에 실패했습니다.";
            }
        }
    }

    //json으로 변환하여 출력(Android로 보내자)
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