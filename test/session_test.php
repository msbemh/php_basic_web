<?php
session_start();
?>

<html>
    안녕하세요 
    <?php 
        // 발급된 세션 id가 있다면 세션의 id를, 없다면 false 반환
        if (isset($_SESSION['nick_name'])) {
            echo $_SESSION['nick_name'];
        }
    ?>
</html>


