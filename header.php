<script>
    $(document).ready(function(){
        const $navbar_nav = $("#navbarNav");
        const $navbar_ul = $navbar_nav.children();
        const $navbar_li_list = $navbar_nav.children().children();

        const current_href_split = window.location.href.split('/');
        const current_last_url = current_href_split[current_href_split.length - 1];

        for(let i=0; i<$navbar_li_list.length; i++){
            const navbar_li = $navbar_li_list[i];
            const $navbar_li = $(navbar_li);
            const navbar_a = navbar_li.children[0];
            const navbar_href_split = navbar_a.href.split('/');
            const navbar_last_url = navbar_href_split[navbar_href_split.length - 1];

            $navbar_li.removeClass('active');

            if(current_last_url === navbar_last_url){
                $navbar_li.addClass('active');
            }
        }
    });
</script>
<div class="header_section">
<div class="container-fluid">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="logo"><a href="/index.php">포토 공유 사이트</a></div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
            <a class="nav-link" href="/index.php">홈</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/free_board/free_board.php">게시판</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="https://turn-stun-server.kro.kr:9090/test">화상채팅</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/user/login.php">로그인</a>
            </li>
        </ul>
    </div>
</nav>
</div>
</div>