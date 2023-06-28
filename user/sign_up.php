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
        <div class="container">
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12">
                    <h1 class="title">회원가입</h1>
                </div>
            </div>
            <div class="container3">
                <div class="flex_container">
                    <div class="font-size3 bold text-right">아이디</div>
                    <input type="text" class="input" />
                </div>
                <div class="text-center">유효한 아이디 입니다.</div>

                <div class="flex_container">
                    <div class="font-size3 bold text-right">비밀번호</div>
                    <input type="text" class="input" />
                </div>
                <div class="text-center">10자리 이상 20자리 미만. 문자,숫자,알파벳 조합</div>

                <div class="flex_container">
                    <div class="font-size3 bold text-right">비밀번호 확인</div>
                    <input type="text" class="input" />
                </div>
                <div class="text-center">비밀번호가 일치 합니다.</div>

                <div class="flex_container">
                    <div class="font-size3 bold text-right">닉네임</div>
                    <input type="text" class="input" />
                </div>
                <div class="text-center">유효한 닉네임 입니다.</div>
            </div>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>