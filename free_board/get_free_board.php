<?php 
    require '../db/db_conn.php'; 

    $current_page = intval($_GET['page']);
    $category_name = $_GET['category_name'];
    $category_value = $_GET['category_value'];

    $result_obj = array(
        'current_page' => 1,
        'start_page' => 1,
        'end_page' => 10,
        'is_enable_prev' => false,
        'is_enable_next' => false,
        'next_page' => 11,
        'prev_page' => 1,
        'category_name' => $category_name,
        'category_value' => $category_value,
        'list' => []
    );

    

    // 페이지 디폴트 값은 1
    if (empty($current_page)) {
        $current_page = 1;
    }
    $result_obj['current_page'] = $current_page;

    // 1페이지당 포스트 개수
    $posts_per_page = 10;
    // 페이지 리스트 표현 개수
    $display_page_count = 10;

    // 총 개수 가져온다
    $cnt = 0;
    $sql = "SELECT
            count(id) as cnt
        FROM free_board_tb ";
    if (!empty($category_name) && !empty($category_value)) {
        $sql .= "WHERE $category_name LIKE '%$category_value%' ";
    }


    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = $result->fetch_row();
        $cnt = $row[0];
    }

    /**
     * [start_page]
     * 
     * 몫 : (현재페이지)/(페이지 표현 개수)
     * 나머지 : (현재페이지)%(페이지 표현 개수)
     * 
     * 나머지 0이 아닐 때,
     * start_page = (몫 * 페이지 표현 개수) + 1
     * 
     * 나머지 0일 때,
     * start_page = ((몫 - 1) * 페이지 표현 개수) + 1
     */
    $start_page = 1;
    $share = floor($current_page / $display_page_count);
    $remainder = $current_page % $display_page_count;
    if ($remainder != 0) {
        $start_page = ($share * $display_page_count) + 1;
    } else {
        $start_page = (($share - 1) * $display_page_count) + 1;
    }
    $result_obj['start_page'] = $start_page;

    $end_page = $start_page + $display_page_count - 1;
    $max_end_page = ceil($cnt / $display_page_count);
    $end_page = min($end_page, $max_end_page);
    $result_obj['end_page'] = $end_page;

    // prev 버튼 활성화 여부
    $is_enable_prev = $start_page - $display_page_count > 0 ? true : false;
    $result_obj['is_enable_prev'] = $is_enable_prev;
    // next 버튼 활성화 여부
    $is_enable_next = $max_end_page > $end_page ? true : false;
    $result_obj['is_enable_next'] = $is_enable_next;

    // next 버튼 눌렀을 때 이동할 page
    $next_page = $start_page + $display_page_count;
    $result_obj['next_page'] = $next_page;
    // prev 버튼 눌렀을 때 이동할 page
    $prev_page = ($start_page - $display_page_count) > 0 ? ($start_page - $display_page_count) : 1;
    $result_obj['prev_page'] = $prev_page;

    $offset = ($current_page - 1) * $posts_per_page;
    $limit = $posts_per_page;

    $sql = "SELECT
            id
            ,title
            ,views
            ,create_user
            ,create_date
        FROM free_board_tb ";

    if (!empty($category_name) && !empty($category_value)) {
        $sql .= "WHERE $category_name LIKE '%$category_value%' ";
    }

    $sql .= "ORDER BY create_date DESC 
             LIMIT $limit OFFSET $offset ";

    $result = mysqli_query($conn, $sql);

    /**
     * MYSQLI_ASSOC : 연관된 키가 있는 배열로 반환
     * MYSQLI_NUM : 숫자 인덱스 배열로 반환
     * MYSQLI_BOTH : 키와 숫자 둘다 있는 배열로 반환
     */
    while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($result_obj['list'], $r);
        // $rows[] = $r;
    }

    // json으로 변환하여 출력(Android로 보내자)
    echo json_encode($result_obj);
    
?>
