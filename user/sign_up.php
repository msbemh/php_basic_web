<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        var is_valid_id = false;
        var is_valid_password = false;
        var is_valid_password_chk = false;
        var is_valid_nick_name = false;
        var timeoutId;
        var sec = 0;
        $(document).on('ready', function () {
            init();

            $('#email_input').on('blur', function () {
                const value = this.value;
                const is_valid_email_format = validateEmail(value);
                if (is_valid_email_format) {
                    const type = 'email';
                    $.ajax({
                        url: `get_chk_dupl.php?type=${type}&value=${value}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            window.is_valid_id = data.is_valid;
                            if (window.is_valid_id) {
                                document.querySelector('#email_verify_txt').style.color = 'green';
                                document.querySelector('#email_verify_txt').innerHTML = '유효한 이메일 입니다.';
                            } else {
                                document.querySelector('#email_verify_txt').style.color = 'red';
                                document.querySelector('#email_verify_txt').innerHTML = '중복된 이메일 입니다.';
                            }

                        },
                        error: function (xhr, status, error) {
                            window.is_valid_id = false;
                            document.querySelector('#email_verify_txt').style.color = 'red';
                            document.querySelector('#email_verify_txt').innerHTML = '서버 에러';
                            console.log(error);
                        },
                        complete: function () {
                            document.querySelector('#email_verify_txt').style.display = 'block';
                        }
                    });
                } else {
                    document.querySelector('#email_verify_txt').style.display = 'block';
                    document.querySelector('#email_verify_txt').style.color = 'red';
                    document.querySelector('#email_verify_txt').innerHTML = '이메일 형식이 맞지 않습니다.';
                }
            });

            $('#password_input').on('blur', function () {
                const value = this.value;
                window.is_valid_password = validPassword(value);
                document.querySelector('#password_verify_txt').style.display = 'block';
                if (window.is_valid_password) {
                    document.querySelector('#password_verify_txt').style.color = 'green';
                    document.querySelector('#password_verify_txt').innerHTML = '유효한 비밀번호 입니다.';
                } else {
                    document.querySelector('#password_verify_txt').style.color = 'red';
                    document.querySelector('#password_verify_txt').innerHTML = '유효하지 않는 비밀번호 형식 입니다.';
                }

                const password_chk_value = document.querySelector('#password_chk_input').value;
                if (!is_empty(password_chk_value)) {
                    $('#password_chk_input').trigger('blur');
                }

            });

            $('#password_chk_input').on('blur', function () {
                const value = this.value;
                const password = document.querySelector('#password_input').value;
                const password_chk = document.querySelector('#password_chk_input').value;

                if (!window.is_valid_password) {
                    document.querySelector('#password_chk_verify_txt').style.display = 'block';
                    document.querySelector('#password_chk_verify_txt').style.color = 'red';
                    document.querySelector('#password_chk_verify_txt').innerHTML = '유효하지 않는 비밀번호 형식 입니다.';
                    return;
                }

                if (password === password_chk) {
                    window.is_valid_password_chk = true;
                } else {
                    window.is_valid_password_chk = false;
                }
                document.querySelector('#password_chk_verify_txt').style.display = 'block';

                if (window.is_valid_password_chk) {
                    document.querySelector('#password_chk_verify_txt').style.color = 'green';
                    document.querySelector('#password_chk_verify_txt').innerHTML = '비밀번호가 일치합니다.';
                } else {
                    document.querySelector('#password_chk_verify_txt').style.color = 'red';
                    document.querySelector('#password_chk_verify_txt').innerHTML = '비밀번호가 일치하지 않습니다.';
                }
            });

            $('#nick_name_input').on('blur', function () {
                const value = this.value;
                const type = 'nick_name';

                // 빈값 체크
                if (is_empty(value)) {
                    document.querySelector('#nick_name_verify_txt').style.display = 'block';
                    document.querySelector('#nick_name_verify_txt').style.color = 'red';
                    document.querySelector('#nick_name_verify_txt').innerHTML = '닉네임을 입력하세요';
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

            $('#auth_req_btn').on('click', function () {
                const email = document.querySelector('#email_input').value;
                const nick_name = document.querySelector('#nick_name_input').value;

                if (!window.is_valid_id) {
                    Swal.fire(
                        '이메일 인증 필요',
                        '유효한 이메일을 먼저 입력하세요',
                        'warning'
                    );
                    return;
                }

                $.ajax({
                    url: `send_email.php?email=${email}&nick_name=${nick_name}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            Swal.fire(
                                '이메일 인증번호 보내기',
                                '성공',
                                'success'
                            );

                            const auth_sec_element = document.querySelector('#auth_sec');
                            auth_sec_element.style.display = 'block';

                            // interval이 이미 존재한다면 clear
                            if (window.timeoutId) clearInterval(window.timeoutId);

                            // interval 시작
                            window.sec = 60;
                            window.timeoutId = setInterval(() => {
                                auth_sec_element.innerHTML = `유효시간: ${sec}초`;
                                window.sec -= 1;
                                if (window.sec <= 0) {
                                    window.sec = 0;
                                    auth_sec_element.innerHTML = `인증 만료`;
                                    clearInterval(window.timeoutId);
                                }

                            }, 1000);
                        } else {
                            Swal.fire(
                                '이메일 인증 실패',
                                data.msg,
                                'fail'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire(
                            '이메일 인증 에러',
                            error,
                            'fail'
                        );
                        console.log(error);
                    },
                    complete: function () {
                    }
                });
            });

            $('#sign_up_btn').on('click', function () {
                if (!window.is_valid_id) {
                    Swal.fire(
                        '이메일 유효성',
                        '이메일이 유효하지 않습니다.',
                        'warning'
                    );
                    return;
                }
                if (!window.is_valid_password) {
                    Swal.fire(
                        '비밀번호 유효성',
                        '비밀번호가 유효하지 않습니다.',
                        'warning'
                    );
                    return;
                }
                if (!window.is_valid_password_chk) {
                    Swal.fire(
                        '비밀번호 유효성',
                        '비밀번호가 일치하지 않습니다.',
                        'warning'
                    );
                    return;
                }
                if (!window.is_valid_nick_name) {
                    Swal.fire(
                        '닉네임 유효성',
                        '닉네임이 유효하지 않습니다.',
                        'warning'
                    );
                    return;
                }
                if (!window.timeoutId) {
                    Swal.fire(
                        '인증번호 유효성',
                        '인증번호가 유효하지 않습니다.',
                        'warning'
                    );
                    return;
                }

                const email = document.querySelector('#email_input').value;
                const password = document.querySelector('#password_input').value;
                const nick_name = document.querySelector('#nick_name_input').value;
                const auth_num = document.querySelector('#auth_num_input').value;

                // 회원가입
                $.ajax({
                    url: `post_sign_up.php`,
                    type: 'POST',
                    data: {
                        email,
                        password,
                        nick_name,
                        auth_num
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            Swal.fire(
                                '회원가입',
                                '완료',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'login.php';
                                }
                            });

                        } else {
                            Swal.fire(
                                '회원가입 실패',
                                data.msg,
                                'fail'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire(
                            '회원가입 에러',
                            error,
                            'fail'
                        );
                        console.log(error);
                    },
                    complete: function () {
                    }
                });
            });

        });

        function init() {
            document.querySelector('#email_verify_txt').style.display = 'none';
            document.querySelector('#password_verify_txt').style.display = 'none';
            document.querySelector('#password_chk_verify_txt').style.display = 'none';
            document.querySelector('#nick_name_verify_txt').style.display = 'none';
        }

        function is_empty(str) {
            return (str == '' || str == undefined || str == null || str == 'null');
        }

        function validPassword(password) {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;
            return passwordRegex.test(password);
        }

        function validateEmail(email) {
            const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            return emailRegex.test(email);
        }

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="login_section layout_padding">
        <div class="login-form-container">
            <div class="row" style="margin-top:120px;">
                <div class="col-sm-12">
                    <h1 class="title">회원가입</h1>
                </div>
            </div>
            <div style="position: absolute; left: 0px;">
                <div class="flex_container">
                    <div class="font-size3 bold text-right">이메일</div>
                    <input id="email_input" type="text" class="input" />
                </div>
                <div id="email_verify_txt" class="text-center margin-top-10" style="margin-left:134px;"></div>

                <div class="flex_container margin-top">
                    <div class="font-size3 bold text-right ">비밀번호</div>
                    <input id="password_input" type="password" class="input" />
                </div>
                <div id="password_verify_txt" class="text-center margin-top-10" style="margin-left:134px;">
                </div>

                <div class="flex_container margin-top">
                    <div class="font-size3 bold text-right ">비밀번호 확인</div>
                    <input id="password_chk_input" type="password" class="input" />
                </div>
                <div id="password_chk_verify_txt" class="text-center margin-top-10" style="margin-left:134px;"></div>

                <div class="flex_container margin-top">
                    <div class="font-size3 bold text-right ">닉네임</div>
                    <input id="nick_name_input" type="text" class="input" />
                </div>
                <div id="nick_name_verify_txt" class="text-center margin-top-10" style="margin-left:134px;"></div>

                <div class="flex_container margin-top">
                    <div class="font-size3 bold text-right ">인증번호</div>
                    <input id="auth_num_input" type="text" class="input" />
                </div>
                <div id="auth_req_btn" class="btn btn-dark" style="position: absolute; right: -67px; bottom: 93px;">요청
                </div>
                <div id="auth_sec" style="position: absolute; right: -180px; bottom: 98px; color:black; display:none;">
                    유효기간: 60초</div>
                <div id="sign_up_btn" class="seemore_bt" style="margin-left: 188px;"><a href="#">회원가입</a></div>
            </div>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>