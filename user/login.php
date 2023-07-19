<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        $(document).on('ready', function () {
            $('#login_btn').on('click', function () {
                login();
            });

        });

        function is_empty(str) {
            return (str == '' || str == undefined || str == null || str == 'null');
        }

        function go_sign_up() {
            window.location.href = 'sign_up.php'
        }

        function login() {
            const email = document.querySelector('#email_input').value;
            const password = document.querySelector('#password_input').value;

            // 로그인
            $.ajax({
                url: `post_login.php`,
                type: 'POST',
                data: {
                    email,
                    password
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result) {
                        Swal.fire(
                            '로그인',
                            '완료',
                            'success'
                        ).then((result) => {
                            window.location.href = '/free_board/free_board.php';
                        });

                    } else {
                        Swal.fire(
                            '로그인 실패',
                            data.msg,
                            'fail'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        '로그인 에러',
                        error,
                        'fail'
                    );
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

    <div class="login_section layout_padding">
        <div class="login-form-container">
            <div class="row" style="margin-top:120px;">
                <div class="col-sm-12">
                    <h1 class="title">로그인</h1>
                </div>
            </div>
            <div style="position: absolute; left: 0px;">
                <div class="flex_container">
                    <div class="font-size3 bold text-right">이메일</div>
                    <input id="email_input" type="text" class="input" />
                </div>

                <div class="flex_container margin-top">
                    <div class="font-size3 bold text-right">비밀번호</div>
                    <input id="password_input" type="password" class="input" />
                </div>
            </div>
            <div id="login_btn" class="seemore_bt" style="margin-top: 90px;"><a href="#">로그인</a></div>
            <hr>
            <div class="flex_container">
                <div class="pointer" onclick="go_sign_up()">회원가입</div>
                <div class="pointer">비밀번호 찾기</div>
            </div>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>