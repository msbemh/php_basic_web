<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        var is_valid_nick_name = true;
        var already_file_list = [];
        var new_file_list = [];
        var remove_file_list = [];

        $(document).on('ready', function () {
            const user_profile_img = document.querySelector('#user_profile_img');
            if (!is_empty(session_profile_img)) {
                user_profile_img.src = session_profile_img;
                already_file_list.push({
                    path: session_profile_img,
                    type: 'already',
                    name: session_profile_img.split('/').pop()
                });
            } else {
                user_profile_img.src = "https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77";
            }

            if (is_empty(session_email)) {
                Swal.fire(
                    '세션',
                    '없음',
                    'warning'
                ).then((result) => {
                    window.location.href = '/free_board/free_board.php';
                });
            }

            document.querySelector('#email_txt').innerHTML = session_email;
            document.querySelector('#nick_name_input').value = session_nick_name;

            $('#nick_name_input').on('blur', function () {
                const value = this.value;
                const type = 'nick_name';

                // 빈값 체크
                if (is_empty(value)) {
                    document.querySelector('#nick_name_verify_txt').style.display = 'block';
                    document.querySelector('#nick_name_verify_txt').style.color = 'red';
                    document.querySelector('#nick_name_verify_txt').innerHTML = '닉네임을 입력하세요';
                    window.is_valid_nick_name = false;
                    return;
                }

                $.ajax({
                    url: `get_chk_dupl.php?type=${type}&value=${value}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        window.is_valid_nick_name = data.is_valid;
                        if (window.is_valid_nick_name) {
                            document.querySelector('#nick_name_verify_txt').style.color = 'green';
                            document.querySelector('#nick_name_verify_txt').innerHTML = '유효한 닉네임 입니다.';
                        } else {
                            document.querySelector('#nick_name_verify_txt').style.color = 'red';
                            document.querySelector('#nick_name_verify_txt').innerHTML = '중복된 닉네임 입니다.';
                        }

                    },
                    error: function (xhr, status, error) {
                        window.is_valid_nick_name = false;
                        document.querySelector('#nick_name_verify_txt').style.color = 'red';
                        document.querySelector('#nick_name_verify_txt').innerHTML = '서버 에러';
                        console.log(error);
                    },
                    complete: function () {
                        document.querySelector('#nick_name_verify_txt').style.display = 'block';
                    }
                });
            });

            $('#my_page_update_btn').on('click', function () {
                const nick_name = document.querySelector('#nick_name_input').value;

                const formData = new FormData();
                formData.append('nick_name', nick_name);

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
                    url: `post_user_update.php`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            Swal.fire(
                                '프로필 수정',
                                '완료',
                                'success'
                            ).then((result) => {
                                window.location.reload();
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

            $('#user_profile_img').on('click', function () {
                $('#img_upload_modal').modal('show');
            });


        });

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="free_board_section layout_padding">
        <div style="position:relative; width: 500px; margin:0 auto;">
            <!-- 제목 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="gallery_taital">마이페이지</h1>
                </div>
            </div>
            <!-- 사용자 프로필 사진 -->
            <div class="user_profile_box user_profile_center_box">
                <img class="pointer" id="user_profile_img">
            </div>
            <!-- 이메일 -->
            <div style="display:flex; align-items: center; gap: 1rem; position:relative; left: -17px;">
                <div style="width:80px; text-align: right;">이메일</div>
                <div id="email_txt" style="width:350px; background: silver; height: 38px;" class="plain_input"></div>
            </div>
            <!-- 닉네임 -->
            <div class="margin-top"
                style="display:flex; align-items: center; gap: 1rem; position:relative; left: -17px;">
                <div style="width:80px; text-align: right;">닉네임</div>
                <input id="nick_name_input" style="width:350px;" class="plain_input" />
            </div>
            <!-- 닉네임 검증 텍스트란 -->
            <div id="nick_name_verify_txt" class="text-center margin-top" style="display:none;"></div>
            <!-- 저장 버튼 -->
            <div class="margin-top">
                <div id="my_page_update_btn" class="seemore_bt"><a href="#">저장</a></div>
            </div>
        </div>
    </div>
    <!-- gallery section end -->

    <!-- 모달 -->
    <div class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <?php require '../modal/file_user_profile_modal.php'; ?>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>