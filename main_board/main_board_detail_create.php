<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        var already_file_list = [];
        var new_file_list = [];
        var remove_file_list = [];
        $(document).on('ready', function () {

            $('#cancle_btn').on('click', function () {
                window.history.back();
            });

            $('#save_btn').on('click', function () {
                const memo = document.querySelector('#memo').value;

                const formData = new FormData();
                formData.append('memo', memo);

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
                    url: `post_create_main_board.php`,
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
                                window.location.href = `main_board_detail_read.php?main_board_id=${data.main_board_id}`;
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

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="free_board_section layout_padding create_free_board">
        <div class="container">
            <!-- 제목및 버튼 컨테이너 -->
            <div class="create_container">
                <div class="board_detail_title" style="width:50%; flex-grow: 1;">게시글 생성</div>
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