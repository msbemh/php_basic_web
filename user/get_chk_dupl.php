<?php 
    require '../db/db_conn.php'; 

    session_start();

    $value= $_GET['value'];
    $type = $_GET['type'];

    $result_obj = array(
        'is_valid' => false
    );

    /**
     * 이메일이나 닉네임이 중복되는지 확인
     */
    $sql = "SELECT
            email
        FROM user_tb ";

    if($type === 'email'){
        $sql = $sql . "WHERE email = '$value' ";
    }else if($type === 'nick_name'){
        $sql = $sql . "WHERE nick_name = '$value' ";
        if(isset($_SESSION['email'])){
            $session_email = $_SESSION['email'];
            $sql = $sql . "AND email != '$session_email' ";
        }
    }
        

    $result = mysqli_query($conn, $sql);
    $rows = [];

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($rows, $r);
    }

    if(count($rows) > 0){
        $result_obj['is_valid'] = false;
    }else {
        $result_obj['is_valid'] = true;
    }

    // json으로 변환하여 출력(Android로 보내자)
    echo json_encode($result_obj);
    
?>
