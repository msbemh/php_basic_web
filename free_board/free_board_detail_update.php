<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        // id 추출
        let id;
        for (const param of new URLSearchParams(location.search)) {
            const key = param[0];
            const value = param[1];
            if (key === 'id') {
                id = value;
            }
        }

        // 에디터
        var editor;

        $(document).on('ready', function () {
            get_board_list_detail();

            /**
            * ID : 'suneditor_sample'
            * ClassName : 'sun-eidtor'
            */
            // ID or DOM object
            window.editor = SUNEDITOR.create((document.getElementById('suneditor') || 'suneditor'), {
                // All of the plugins are loaded in the "window.SUNEDITOR" object in dist/suneditor.min.js file
                // Insert options
                // Language global object (default: en)
                // plugins: [font, video, image, list],
                imageUploadUrl: 'image-upload.php',
                imageMultipleFile: true,
                videoFileInput: true,
                videoUploadUrl: 'vedio_upload.php',
                videoMultipleFile: true,
                buttonList: [
                    ['undo', 'redo', 'font', 'fontSize', 'formatBlock'],
                    ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript', 'removeFormat'],
                    ['fontColor', 'hiliteColor', 'outdent', 'indent', 'align', 'horizontalRule', 'list', 'table'],
                    ['link', 'image', 'video', 'fullScreen', 'showBlocks', 'codeView', 'preview', 'print', 'save']
                ],
                height: 400,
                lang: SUNEDITOR_LANG['ko']
            });

            $('#cancle_btn').on('click', function () {
                window.history.back();
            });

            $('#save_btn').on('click', function () {
                const title = document.querySelector('#title').value;
                const content = editor.getContents();
                const file_path_infos = [];

                const is_notice = document.querySelector('#is_notice').checked;

                let type = 'normal';
                if(is_notice){
                    type = 'notice';
                }else{
                    type = 'normal';
                }

                /**
                 * suneditor에 추가한 image file 경로 추가
                 */
                const suneditor_img_infos = editor.getImagesInfo();
                suneditor_img_infos.forEach(info => {
                    const pathname = new URL(info.src).pathname;
                    file_path_infos.push({
                        path : pathname,
                        type : 'img'
                    });
                });

                /**
                 * suneditor에 추가한 video file 경로 추가
                 */
                const suneditor_video_infos = editor.getFilesInfo('video');
                suneditor_video_infos.forEach(info => {
                    const pathname = new URL(info.src).pathname;
                    file_path_infos.push({
                        path : pathname,
                        type : 'video'
                    });
                });

                $.ajax({
                    url: 'post_update_free_board.php',
                    type: 'POST',
                    data: {
                        id,
                        title,
                        content,
                        type,
                        file_path_infos
                    },
                    dataType: 'json',
                    success: function (data) {
                        const result = data.result;
                        if (result) {
                            window.location.href = `free_board_detail_read.php?id=${id}`;
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            });
        });

        function get_board_list_detail() {
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
                        , type
                        , create_user
                        , create_date
                        , update_user
                        , update_date
                    } = data.data;

                    const files = data.files;

                    const title_element = document.querySelector('#title');
                    title_element.value = title;

                    let is_notice = false;
                    if(type === 'notice'){
                        is_notice = true;
                    }

                    if(is_notice){
                        document.querySelector('#is_notice').checked = true;
                    }else{
                        document.querySelector('#is_notice').checked = false;
                    }

                    

                    editor.insertHTML(content, true, true);
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

    <div class="free_board_section layout_padding create_free_board">
        <div class="container">
            <div class="create_container">
                <div class="board_detail_title" style="width:50%; flex-grow: 1;">게시글 수정</div>
                <div style="flex-grow: 999;"></div>
                <div style="flex-grow: 1;">
                    <button type="button" class="btn btn-dark" id="cancle_btn">취소</button>
                    <button type="button" class="btn btn-dark" id="save_btn">저장</button>
                </div>
            </div>
            <div class="right">
                <input type="checkbox" id="is_notice" name="type">
                <label for="type">공지사항 여부</label>
            </div>
            <hr class="clear">
            <div class="board_detail_content" style="margin-bottom:10px;">
                <input class="plain_input" id="title" placeholder="제목을 입력하세요" />
            </div>
            <div>
                <textarea id="suneditor" class="suneditor"></textarea>
            </div>
        </div>
    </div>
    <!-- gallery section end -->

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>