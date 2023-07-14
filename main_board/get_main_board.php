<?php
require '../db/db_conn.php';

$main_board_id = $_GET['main_board_id'];

$result_obj = array(
    'main_data' => [],
    'file_list' => [],
    'comment_list' => []
);

// $sql = "WITH RECURSIVE CTE (id, parent_id, main_board_id, comment, path, create_user, create_date) AS
//         (
//             SELECT id, parent_id, main_board_id, comment, CAST(id AS CHAR(200)), create_user, create_date
//                 FROM main_board_comment_tb
//                 WHERE parent_id IS NULL
//             UNION ALL
//             SELECT B.id, B.parent_id, B.main_board_id, B.comment, CONCAT(A.path, ',', B.id), B.create_user, B.create_date
//                 FROM CTE AS A 
//                 INNER JOIN main_board_comment_tb AS B
//                 ON A.id = B.parent_id
//         )
//         SELECT
//             id, 
//             parent_id, 
//             main_board_id, 
//             comment,
//             A.create_user,
//             B.nick_name,
//             B.create_date,
//             concat(path, '@') as path
//         FROM CTE as A
//         LEFT OUTER JOIN user_tb B 
//         ON A.create_user = B.email
//         WHERE main_board_id = '$main_board_id'
//         ORDER BY path DESC ";

$sql = "SELECT 
            A.id,
            A.parent_id,
            A.main_board_id,
            A.comment,
            A.create_user,
            A.create_date,
            B.nick_name,
            B.profile_img as create_profile_img,
            (SELECT COUNT(*) FROM main_board_comment_tb C
                WHERE A.id = C.parent_id) as comment_cnt
        FROM main_board_comment_tb A
        LEFT OUTER JOIN user_tb B 
        ON A.create_user = B.email
        WHERE parent_id is null
        AND main_board_id = '$main_board_id'
        ORDER BY A.create_date ASC ";

$result = mysqli_query($conn, $sql);

/**
 * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
 * MYSQLI_NUM : 숫자 인덱스 배열로 반환
 * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
 */
while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($result_obj['comment_list'], $r);
}


$sql = "SELECT
            A.id,
            A.memo,
            A.create_user,
            B.nick_name,
            B.profile_img as create_profile_img
        FROM main_board_tb A
        LEFT OUTER JOIN user_tb B 
        ON A.create_user = B.email
        WHERE id = '$main_board_id' ";

$result = mysqli_query($conn, $sql);

/**
 * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
 * MYSQLI_NUM : 숫자 인덱스 배열로 반환
 * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
 */
while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($result_obj['main_data'], $r);
}



$sql = "SELECT
            id,
            path
        FROM main_board_file_tb
        WHERE main_board_id = '$main_board_id' ";

$result = mysqli_query($conn, $sql);

/**
 * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
 * MYSQLI_NUM : 숫자 인덱스 배열로 반환
 * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
 */
while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($result_obj['file_list'], $r);
}

// json으로 변환하여 출력(Android로 보내자)
echo json_encode($result_obj);

?>