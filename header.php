<script>
    $(document).ready(function () {
        const $navbar_nav = $("#navbarNav");
        const $navbar_ul = $navbar_nav.children();
        const $navbar_li_list = $navbar_nav.children().children();

        const current_href_split = window.location.href.split('/');
        const current_last_url = current_href_split[current_href_split.length - 1];

        for (let i = 0; i < $navbar_li_list.length; i++) {
            const navbar_li = $navbar_li_list[i];
            const $navbar_li = $(navbar_li);
            const navbar_a = navbar_li.children[0];
            
            if(!navbar_a.href) return;

            const navbar_href_split = navbar_a.href.split('/');
            const navbar_last_url = navbar_href_split[navbar_href_split.length - 1];

            $navbar_li.removeClass('active');

            if (current_last_url === navbar_last_url) {
                $navbar_li.addClass('active');
            }
        }

    });

    function go_my_page(e) {
        window.location.href = '/user/my_page.php';
    }

    function logout() {
        $.ajax({
            url: '/user/post_log_out.php',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                const result = data.result;
                if (result) {
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    }
</script>
<div class="header_section">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="logo"><a href="/index.php">커뮤니티!!</a></div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item relative_box">
                        <a class="nav-link position_center" href="/index.php">홈</a>
                    </li>
                    <li class="nav-item relative_box">
                        <a class="nav-link position_center" href="/main_board/main_board_detail_create.php">생성</a>
                    </li>
                    <li class="nav-item relative_box">
                        <a class="nav-link position_center" href="/free_board/free_board.php">게시판</a>
                    </li>
                    <li class="nav-item relative_box">
                        <a class="nav-link position_center" href="https://turn-stun-server.kro.kr:9090/test">화상채팅</a>
                    </li>
                    <li class="nav-item relative_box">
                        <?php
                        session_start();
                        // 발급된 세션 id가 있다면 세션의 id를, 없다면 false 반환
                        if (isset($_SESSION['email'])) {
                            // echo '<a class="nav-link" >'.$_SESSION['nick_name'].'</a>';
                        
                            $profile_img = "https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77";
                            if ($_SESSION['profile_img']) {
                                $profile_img = $_SESSION['profile_img'];
                            }

                            // <div class="thumb_area">
                            // <a class="thumb">
                            //     <img src="' . $profile_img . '"
                            //         alt="프로필 사진" width="36" height="36">
                            // </a>
                            // </div>

                            // <img style="margin:10px; width: 60px; height: 60px; flex-direction: row;"
                            //                     src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77"
                            //                     class="card-img-top pointer">
                            echo
                                '<div class="dropdown position_center">
                                <div class="WriterInfo pointer" style="margin-left: 25px;" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <div class="user_profile_header_box ">
                                        <img class="pointer" src="' . $profile_img . '">
                                    </div>
                                </div>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <div class="card" style="width: 18rem;">
                                        <div style="display:flex; align-items: center; margin-left: 18px;">
                                            <div class="user_profile_header_box ">
                                                <img class="pointer" src="' . $profile_img . '">
                                            </div>
                                            <div style="display:flex; flex-direction: column; margin:10px;">
                                                <div class="">' . $_SESSION['nick_name'] . '</div>
                                                <div class="">' . $_SESSION['email'] . '</div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <button onclick="go_my_page()" class="btn btn-dark pointer" >마이페이지</button>
                                        </div>
                                    </div>
                                    <a onclick="logout()" class="dropdown-item pointer" >로그아웃</a>
                                </div>
                            </div>';
                        } else {
                            echo '<a class="nav-link" href="/user/login.php">로그인</a>';
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>