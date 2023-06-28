<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>
        $(document).on('ready', function () {


        });

        function is_empty(str) {
            return (str == '' || str == undefined || str == null || str == 'null');
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
            <div style="position: absolute; left: 23px;">
                <div class="flex_container">
                    <div class="font-size3 bold text-right">아이디</div>
                    <input type="text" class="input" />
                </div>

                <div class="blank"></div>

                <div class="flex_container">
                    <div class="font-size3 bold text-right">비밀번호</div>
                    <input type="text" class="input" />
                </div>
            </div>
            <div class="seemore_bt" style="margin-top: 90px;"><a href="#">로그인</a></div>
            <hr>
            <div class="flex_container">
                <div>회원가입</div>
                <div>비밀번호 찾기</div>
            </div>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>