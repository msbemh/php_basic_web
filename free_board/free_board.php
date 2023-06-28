<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <?php require '../user/session.php'; ?>
    <script>
        /**
         * 게시판 페이징 및 검색 변수들
         */
        var next_page;
        var prev_page;
        var is_enable_next;
        var is_enable_prev;
        /**
         * 검색 버튼을 클릭하기 전의 카테고리 값과
         * 검색 이후의 카테고리 값을 나누었다.
         * 
         * 이유 : 페이징에서는 검색 이후의 카테고리 값으로 적용이 필요
         */
        var current_category_name = 'title';
        var category_name_after_search;
        var category_value_after_search;

        $(document).on('ready', function () {
            // 처음 진입시, 기본 게시판 데이터 로드
            get_board_list(1);

            $(".dropdown-item").on('click', function () {
                window.current_category_name = this.dataset.category;
                const dropdown_menu_button = document.querySelector('#dropdownMenuButton');
                const text = this.innerHTML;

                // 카테고리 txt 수정
                if (window.current_category_name === 'title') {
                    dropdown_menu_button.innerHTML = text;
                } else if (window.current_category_name === 'create_user') {
                    dropdown_menu_button.innerHTML = text;
                }
            });

            $("#search_btn").on('click', function () {
                const search_input = document.querySelector('.search_input');
                const value = search_input.value;
                // if (is_empty(value)) {
                //     alert('검색할 단어를 입력하세요.');
                //     return;
                // }

                get_board_list(1, {
                    category_name: window.current_category_name,
                    category_value: value
                });

            });

            $("#create_btn").on('click', function () {
                window.location.href = 'free_board_detail_create.php'
            });

        });

        function is_empty(str) {
            return (str == '' || str == undefined || str == null || str == 'null');
        }

        function move_prev(event) {
            // 링크의 기본 동작을 막음
            event.preventDefault();
            if (!window.is_enable_prev) return;
            get_board_list(window.prev_page, {
                category_name: window.category_name_after_search,
                category_value: window.category_value_after_search
            });
        }

        function move_next(event) {
            // 링크의 기본 동작을 막음
            event.preventDefault();

            if (!window.is_enable_next) return;

            get_board_list(window.next_page, {
                category_name: window.category_name_after_search,
                category_value: window.category_value_after_search
            });
        }

        function row_click(){
            const id = this.dataset.id;
            window.location.href = `free_board_detail_read.php?id=${id}`;
        }

        function get_board_list(page, category) {
            /**
             * 카테고리가 존재할 경우, 파라미터로 카테고리 정보를 넘겨준다
             * 카테고리가 없을 경우, 파라미터로 페이지 정보만 넘겨준다
             */
            let url;
            if (category && category.category_name && category.category_value) {
                url = `/free_board/get_free_board.php?page=${page}&category_name=${category.category_name}&category_value=${category.category_value}`;
            } else {
                url = `/free_board/get_free_board.php?page=${page}`;
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    /**
                     * 서버에서 게시판 데이터 리스트와
                     * 페이징 정보를 가져온다.
                     */
                    let {
                        start_page,
                        end_page,
                        is_enable_prev,
                        is_enable_next,
                        next_page,
                        prev_page,
                        list
                    } = data;

                    /**
                     * 검색 완료된 이후의 카테고리값 변수에 저장하여
                     * 페이징 번호 클릭시, 카테고리 값을 서버로 계속 보내줄 수 있도록 한다
                     */
                    if (data.category_name && data.category_value) {
                        window.category_name_after_search = data.category_name;
                        window.category_value_after_search = data.category_value;
                    } else {
                        window.category_name_after_search = data.category_name;
                        window.category_value_after_search = data.category_value;
                    }

                    const current_page = page;

                    window.next_page = next_page;
                    window.prev_page = prev_page;
                    window.is_enable_next = is_enable_next;
                    window.is_enable_prev = is_enable_prev;

                    /**
                     * 게시판 동적 생성
                     */
                    const table_body = document.querySelector('#free_board tbody');
                    // 자식 노드 전부 삭제
                    table_body.replaceChildren();

                    // 게시글 렌더링
                    for (const row of list) {
                        const {
                            id,
                            title,
                            views,
                            create_user,
                            create_date
                        } = row;
                        const tr = document.createElement('tr');

                        const id_td = document.createElement('td');
                        const id_txt = document.createTextNode(id);
                        id_td.appendChild(id_txt);
                        tr.appendChild(id_td);
                        tr.setAttribute('data-id', id);

                        const title_td = document.createElement('td');
                        const title_txt = document.createTextNode(title);
                        title_td.appendChild(title_txt);
                        tr.appendChild(title_td);

                        const views_td = document.createElement('td');
                        const views_txt = document.createTextNode(views);
                        views_td.appendChild(views_txt);
                        tr.appendChild(views_td);

                        const create_user_td = document.createElement('td');
                        const create_user_txt = document.createTextNode(create_user);
                        create_user_td.appendChild(create_user_txt);
                        tr.appendChild(create_user_td);

                        const create_date_td = document.createElement('td');
                        const create_date_txt = document.createTextNode(create_date);
                        create_date_td.appendChild(create_date_txt);
                        tr.appendChild(create_date_td);

                        tr.onclick = row_click;

                        table_body.appendChild(tr);
                    }

                    /**
                     * 페이징 렌더링
                     */
                    const pagination_ul = document.querySelector('ul.pagination');
                    pagination_ul.replaceChildren();

                    // 이전 버튼 렌더링
                    const li_prev = document.createElement('li');
                    li_prev.classList.add("page-item");
                    if (!is_enable_prev) {
                        li_prev.classList.add("disabled");
                    }
                    li_prev.onclick = move_prev;

                    const a_prev = document.createElement('a');
                    a_prev.classList.add("page-link", "prev");

                    const a_prev_txt = document.createTextNode("Previous");
                    a_prev.appendChild(a_prev_txt);

                    li_prev.appendChild(a_prev);
                    pagination_ul.appendChild(li_prev);

                    // 페이징 번호 렌더링
                    for (let i = start_page; i <= end_page; i++) {
                        if (i > end_page) {
                            break;
                        }
                        const li_page = document.createElement('li');
                        li_page.classList.add("page-item");
                        // 현재 페이징 화면 활성화
                        if (current_page == i) {
                            li_page.classList.add("active");
                        }

                        // const a_page = document.createElement('a');
                        // a_page_txt = document.createTextNode(i);
                        // a_page.classList.add("page-link");
                        // a_page.onclick = get_board_list;
                        // a_page.appendChild(a_page_txt);

                        // 동적으로 onclick event에 파라미터를 넘겨주기 위해서 string 형식으로 추가 하였다.
                        li_page.insertAdjacentHTML("beforeend", `<a class="page-link" onclick="get_board_list(${i},{
                            category_name : window.category_name_after_search,
                            category_value : window.category_value_after_search
                        })">${i}</a>`);

                        pagination_ul.appendChild(li_page);
                    }

                    // 다음 버튼 렌더링
                    const li_next = document.createElement('li');
                    li_next.classList.add("page-item");
                    if (!is_enable_next) {
                        li_next.classList.add("disabled");
                    }
                    li_next.onclick = move_next;

                    const a_next = document.createElement('a');
                    a_next.classList.add("page-link", "next");

                    const a_next_txt = document.createTextNode("Next");
                    a_next.appendChild(a_next_txt);

                    li_next.appendChild(a_next);
                    pagination_ul.appendChild(li_next);

                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

        // search input enter키 눌렸을 경우, 검색을 시도한다
        function search_input_enter(event) {
            if (event.key === "Enter") {
                $("#search_btn").trigger('click');
            }
        }
    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <!-- gallery section start -->
    <div class="free_board_section layout_padding">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="gallery_taital">자유 게시판</h1>
                </div>
            </div>
            <div class="container3">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        제목
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" data-category="title">제목</a>
                        <a class="dropdown-item" data-category="create_user">작성자</a>
                    </div>
                </div>
                <input type="text" class="search_input" onkeydown="search_input_enter(event)" />
                <button type="button" class="btn btn-dark" id="search_btn">검색</button>
                <button type="button" class="btn btn-dark" id="create_btn">글쓰기</button>
            </div>
            <div class="">
                <table id="free_board" style="width:100%">
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>제목</th>
                            <th>조회수</th>
                            <th>작성자</th>
                            <th>작성일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td>1</td>
                            <td>Maria AndersMaria AndersMaMaria AndersMaria AndersMaria Andersria AndersMaria
                                AndersMaria Anders</td>
                            <td>99</td>
                            <td>Germany</td>
                            <td>2023-06-23 09:13:44</td>
                        </tr> -->
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                <!-- 페이징 -->
                <div class="container2">
                    <nav class="page_navigation" aria-label="Page navigation example">
                        <ul class="pagination">
                            <!-- <li class="page-item"><a class="page-link" href="#">1</a></li> -->
                            <!-- <li class="page-item"><a class="page-link" href="#">2</a></li> -->
                            <!-- <li class="page-item"><a class="page-link" href="#">3</a></li> -->
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="seemore_bt"><a href="#">See More</a></div>
        </div>
    </div>
    <!-- gallery section end -->

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>