<?php
// 발급된 세션 id가 있다면 세션의 id를, 없다면 false 반환
if (!session_id()) {
    // id가 없을 경우 세션 시작
    session_start();
}
?>