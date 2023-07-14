<?php
require '../db/db_conn.php';

$result_obj = array();

$sql = "SELECT 
            D.id
            ,D.title
            ,D.content
            ,D.views
            ,D.create_user
            ,D.type
            ,E.nick_name as create_nick_name
            ,D.create_date
        FROM free_board_tb D
        LEFT OUTER JOIN user_tb as E
        ON D.create_user = E.email 
        WHERE D.type = 'notice'
        ORDER BY create_date DESC ";

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