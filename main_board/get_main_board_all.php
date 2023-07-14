<?php
require '../db/db_conn.php';

session_start();

$email = $_SESSION['email'];

$main_board_id = $_GET['main_board_id'];

$result_obj = array();

$sql = "SELECT
            A.id,
            A.memo,
            B.main_board_id,
            B.path,
            A.create_user,
            C.nick_name,
            C.profile_img,
            A.create_date,
            (
                SELECT COUNT(id) 
                FROM main_board_heart_tb S 
                WHERE A.id = S.main_board_id
            ) AS heart_cnt,
            (
                SELECT COUNT(id) 
                FROM main_board_heart_tb S 
                WHERE A.id = S.main_board_id
                AND S.heart_supplier = '$email'
            ) AS is_heart_click,
            (
                SELECT COUNT(id)
                FROM main_board_follow_tb S
                WHERE S.follower = '$email'
                AND S.followed = A.create_user
            ) AS is_following
        FROM main_board_tb A
        LEFT OUTER JOIN main_board_file_tb B
        ON A.id = B.main_board_id
        LEFT OUTER JOIN user_tb C
        ON A.create_user = C.email
        ORDER BY A.id DESC ";

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