<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>

    <script>
        var already_file_list = [];
        var new_file_list = [];
        var remove_file_list = [];

        //main_board_id 추출
        let main_board_id;
        for (const param of new URLSearchParams(location.search)) {
            const key = param[0];
            const value = param[1];
            if (key === 'main_board_id') {
                main_board_id = value;
            }
        }

        $(document).on('ready', function () {
            init();

            $('#cancle_btn').on('click', function () {
                window.history.back();
            });

            $('#save_btn').on('click', function () {
                const memo = document.querySelector('#memo').value;

                const formData = new FormData();
                formData.append('memo', memo);
                formData.append('main_board_id', main_board_id);

                // 파일 목록을 FormData에 추가합니다.
                for (let i = 0; i < new_file_list.length; i++) {
                    const file = new_file_list[i];
                    formData.append(`file_${i}`, file);
                }

                // 삭제할 파일 경로를 추가합니다
                formData.append('remove_file_list', JSON.stringify(remove_file_list));

                for (let key of formData.keys()) {
                    console.log(key, ":", formData.get(key));
                }

                $.ajax({
                    url: `post_update_main_board.php`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            Swal.fire(
                                '게시글 작성',
                                '완료',
                                'success'
                            ).then((result) => {
                                window.location.href = `main_board_detail_read.php?main_board_id=${main_board_id}`;
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    },
                    complete: function () {
                    }
                });
            });

            $('#add_btn').on('click', function () {
                $('#img_upload_modal').modal('show');
            });
        });

        function init() {
            $.ajax({
                url: `get_main_board.php?main_board_id=${main_board_id}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    const {
                        main_data,
                        file_list
                    } = data;

                    if (!is_empty(main_data) && main_data.length > 0) {
                        document.querySelector('#memo').value = main_data[0].memo;
                    }

                    for (file_data of file_list) {
                        const id = file_data.id;
                        const path = file_data.path;
                        const split_data = path.split('/');
                        const length = split_data.length;

                        const file_name = split_data[length - 1];

                        already_file_list.push({
                            id,
                            path,
                            name: file_name
                        });
                    }

                    /**
                    * 파라미터 1: 파일리스트 데이터
                    * 파라미터 2: 파일추가될 element
                    * 파라미터 3: 'new' or 'already' 값으로 
                    *  이미 저장되어져 있는 파일인지, 새롭게 추가될 파일인지 구분
                    * 파라미터 4: UI만 업데이트할지 데이터까지 업데이트할지 선택
                    *  - true : UI만 업데이트
                    *  - false : UI와 함께 데이터도 수정
                    * 파라미터 5: origin을 기준으로 작업할지
                    *  modal을 기준으로 작업할지 선택
                    *  - true : origin을 기준으로 작업
                    *  - false : modal을 기준으로 작업
                    */
                    file_add(already_file_list, main_grid_container, 'already', false, true);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                },
                complete: function () {
                }
            });
        }

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="free_board_section layout_padding create_free_board">
        <div class="container">
            <!-- 제목및 버튼 컨테이너 -->
            <div class="create_container">
                <div class="board_detail_title" style="width:50%; flex-grow: 1;">게시글 수정</div>
                <div style="flex-grow: 999;"></div>
                <div style="flex-grow: 1;">
                    <button type="button" class="btn btn-white" id="cancle_btn">취소</button>
                    <button type="button" class="btn btn-dark" id="add_btn">사진 추가</button>
                    <button type="button" class="btn btn-dark" id="save_btn">저장</button>
                </div>
            </div>
            <hr>
            <!-- 파일 리스트 보여주는 컨테이너 -->
            <div id="main_grid_container" class="main_grid_container">
            </div>
            <hr>
            <!-- 메모 입력란 -->
            <div class="board_detail_content" style="margin-bottom:10px;">
                <textarea class="plain_input" id="memo" placeholder="메모를 입력하세요"></textarea>
            </div>
        </div>
    </div>

    <?php require '../modal/file_main_board_modal.php'; ?>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>