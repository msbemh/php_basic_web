<?php 
    require '../db/db_conn.php'; 

    $id = $_GET['id'];

    $result_obj = array(
        'files' => [],
        'data' => []
    );

    if (empty($id)) {
        echo json_encode($result_obj);
        return;
    }

    /**
     * 게시글 정보 가져오기
     */
    $sql = "SELECT
            id
            ,title
            ,views
            ,content
            ,type
            ,create_user
            ,create_date
            ,update_user
            ,update_date
        FROM free_board_tb 
        WHERE id = $id ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $result_obj['data'] = $r;
    }

    /**
     * 파일 리스트 가져오기
     */
    $sql = "SELECT
            id
            ,free_board_id
            ,path
            ,create_user
            ,create_date
            ,update_user
            ,update_date
        FROM free_board_file_tb 
        WHERE free_board_id = $id ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($result_obj['files'], $r);
    }

    // json으로 변환하여 출력(Android로 보내자)
    echo json_encode($result_obj);
    
?>
