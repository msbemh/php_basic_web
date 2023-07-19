<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>

    <?php
    $arr_cookie_options = array(
        'expires' => time() + 60 * 60 * 24 * 30
        // ,'path' => '/hello'
        // ,'domain' => '.example.com'
        // leading dot for compatibility or use subdomain
        // ,'secure' => true
        // or false
        // ,'httponly' => true
        // or false
        // ,'samesite' => 'None' // None || Lax  || Strict
    );
    $value = '9090';
    setcookie("TEST_COOKIE2", $value, $arr_cookie_options);
    ?>

    <script>
        $(document).on('ready', function () {
            $('#cookie_send').on('click', function () {
                setCookie(`TEST_COOKIE`, uuidv4(), 1);
            });
        });
    </script>

</head>

<body>
    <?php require '../header.php'; ?>

    <button class="left" id="cookie_send">쿠키 전송</button>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>

</body>

</html>