<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        //id 추출
        let id;
        for (const param of new URLSearchParams(location.search)) {
            const key = param[0];
            const value = param[1];
            if (key === 'id') {
                id = value;
            }
        }

        $(document).on('ready', function () {
            get_board_list_detail(id);

            $('#cancel_button').on('click', function () {
                window.location.href = 'free_board.php';
            });

            $('#update_button').on('click', function () {
                window.location.href = `free_board_detail_update.php?id=${id}`;
            });

            $('#delete_button').on('click', function () {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-dark',
                        cancelButton: 'btn btn-white'
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: '삭제',
                    text: '정말 삭제 하시겠습니까?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '확인',
                    cancelButtonText: '취소',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete_free_board();
                    }
                })
            });
        });

        function delete_free_board() {
            $.ajax({
                url: 'post_delete_free_board.php',
                type: 'POST',
                data: {
                    id
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result) {
                        Swal.fire(
                            '결과',
                            '삭제완료',
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'free_board.php';
                            }
                        });
                    } else {
                        Swal.fire(
                            '결과',
                            '삭제실패',
                            'fail'
                        )
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }


        function get_board_list_detail(id) {
            /**
             * 카테고리가 존재할 경우, 파라미터로 카테고리 정보를 넘겨준다
             * 카테고리가 없을 경우, 파라미터로 페이지 정보만 넘겨준다
             */
            let url = `/free_board/get_free_board_detail.php?id=${id}`;

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
                        id
                        , title
                        , views
                        , content
                        , create_user
                        , create_date
                        , update_user
                        , update_date
                    } = data.data;

                    const files = data.files;

                    const title_element = document.querySelector('.board_detail_title');
                    title_element.innerHTML = title;

                    const content_element = document.querySelector('.board_detail_content');
                    content_element.innerHTML = content;

                    const create_user_element = document.querySelector('.profile_area .create_user');
                    create_user_element.innerHTML = create_user;

                    const create_date_element = document.querySelector('.profile_area .create_date');
                    create_date_element.innerHTML = create_date;

                    const views_element = document.querySelector('.profile_area .count');
                    views_element.innerHTML = views;


                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="free_board_section layout_padding">
        <div class="container">
            <div>
                <div class="board_detail_title"></div>
                <div class="WriterInfo">
                    <div class="thumb_area">
                        <a class="thumb">
                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77"
                                alt="프로필 사진" width="36" height="36">
                        </a>
                    </div>
                    <div class="profile_area">
                        <div class="profile_info">
                            <div class="nick_box">
                                <span class="create_user">
                                    [사용자명]
                                </span><!---->
                            </div>
                        </div>
                        <div class="article_info">
                            <span class="date create_date">[생성일]</span>
                            조회 <span class="count">[조회수]</span>
                        </div>
                    </div>
                    <div class="button_container">
                        <!-- <button type="button" class="btn btn-white" id="cancel_button">취소</button> -->
                        <button type="button" class="btn btn-dark" id="delete_button">삭제</button>
                        <button type="button" class="btn btn-dark" id="update_button">수정</button>
                    </div>
                </div>
            </div>
            <hr>
            <div>
                <div class="board_detail_content"></div>
            </div>
        </div>
    </div>
    <!-- gallery section end -->

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>