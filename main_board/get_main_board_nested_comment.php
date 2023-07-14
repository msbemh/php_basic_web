<?php
require '../db/db_conn.php';

$main_board_id = $_GET['main_board_id'];
$parent_id = $_GET['parent_id'];

$result_obj = array();

$sql = "SELECT 
            A.id,
            A.parent_id,
            A.main_board_id,
            A.comment,
            A.create_user,
            A.create_date,
            B.nick_name,
            B.profile_img as create_profile_img
        FROM main_board_comment_tb A
        LEFT OUTER JOIN user_tb B 
        ON A.create_user = B.email
        WHERE parent_id = '$parent_id'
        AND main_board_id = '$main_board_id'
        ORDER BY A.create_date ASC ";

$result = mysqli_query($conn, $sql);


/**
 * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
 * MYSQLI_NUM : 숫자 인덱스 배열로 반환
 * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
 */
while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($result_obj, $r);
}

// json으로 변환하여 출력(Android로 보내자)
echo json_encode($result_obj);

?>