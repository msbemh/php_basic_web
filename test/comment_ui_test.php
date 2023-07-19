<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>

    <style>
        .add_info_container {
            width: 20px;
            height: 25px;
            background-size: 150px;
            background: url(/images/icon-more.png) no-repeat;
            background-size: 25px;
            cursor: pointer;
            position: absolute;
            top: 0px;
            right: 0px;
            margin: 10px;
        }
    </style>

    <script>
        var already_file_list = [];
        var new_file_list = [];
        var remove_file_list = [];

        //id 추출
        let main_board_id;
        for (const param of new URLSearchParams(location.search)) {
            const key = param[0];
            const value = param[1];
            if (key === 'main_board_id') {
                main_board_id = value;
            }
        }

        $(document).on('ready', function () {

            load();

            $('#cancle_btn').on('click', function () {
                window.history.back();
            });

            $('#post').on('click', function () {
                const comment_input_element = document.querySelector('#comment_input');
                const comment = comment_input_element.value;
                comment_input_element.value = "";

                const comment_content_box = document.querySelector('#comment_content_box');

                $.ajax({
                    url: `post_create_comment.php`,
                    type: 'POST',
                    data: {
                        main_board_id,
                        comment
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.result) {
                            const data = result.data[0];
                            const {
                                id,
                                parent_id,
                                comment,
                                create_user,
                                create_date
                            } = data;

                            comment_ui_add(data, 'desc');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    },
                    complete: function () {
                    }
                });

            });
        });

        function comment_ui_add(data, order) {

            const {
                id,
                parent_id,
                comment,
                nick_name,
                create_user,
                create_date,
                comment_cnt
            } = data;

            let target = document.createElement('div');
            target.classList.add('main_view_comment_item_box');
            target.dataset.id = `main_view_comment_item_box_${id}`;

            const user_profile_content_box = document.createElement('div');
            user_profile_content_box.classList.add('user_profile_content_box');

            const profile_img = document.createElement('img');
            profile_img.src = session_profile_img;
            profile_img.classList.add('pointer');


            user_profile_content_box.appendChild(profile_img);

            target.appendChild(user_profile_content_box);


            const main_view_comment_item_content_box = document.createElement('div');
            main_view_comment_item_content_box.classList.add('main_view_comment_item_content_box');
            main_view_comment_item_content_box.classList.add('relative_box');
            main_view_comment_item_content_box.dataset.id = `main_view_comment_item_content_box_${id}`;


            const nick_name_container = document.createElement('div');
            nick_name_container.classList.add('bold');
            if (is_empty(nick_name)) {
                nick_name_container.innerHTML = session_nick_name;
            } else {
                nick_name_container.innerHTML = nick_name;
            }

            const comment_container = document.createElement('div');
            comment_container.classList.add('black');
            comment_container.classList.add('word_break');
            comment_container.innerHTML = comment;

            const comment_add_info_container = document.createElement('div');

            const time_span = document.createElement('span');
            time_span.classList.add('gray');
            time_span.innerHTML = create_date;

            const nested_comment_post_span = document.createElement('span');
            nested_comment_post_span.classList.add('gray');
            nested_comment_post_span.classList.add('bold');
            nested_comment_post_span.classList.add('pointer');
            nested_comment_post_span.classList.add('margin_left_12');
            nested_comment_post_span.dataset.id = id;
            nested_comment_post_span.onclick = toggle_reply_intput;
            nested_comment_post_span.innerHTML = "댓글달기";

            comment_add_info_container.appendChild(time_span);
            comment_add_info_container.appendChild(nested_comment_post_span);

            main_view_comment_item_content_box.appendChild(nick_name_container);
            main_view_comment_item_content_box.appendChild(comment_container);
            main_view_comment_item_content_box.appendChild(comment_add_info_container);

            if (comment_cnt > 0) {
                replay_count_render(id, comment_cnt, main_view_comment_item_content_box);
                // const nested_comment_view_div = document.createElement('div');
                // nested_comment_view_div.classList.add('replay_count_view');
                // nested_comment_view_div.innerHTML = `댓글 ${comment_cnt}개`;
                // main_view_comment_item_content_box.appendChild(nested_comment_view_div);
            }


            target.appendChild(main_view_comment_item_content_box);

            if (order === 'asc') {
                comment_content_box.appendChild(target);
            } else if (order === 'desc') {
                comment_content_box.prepend(target);
            }

            add_reply_input(id);

            const nested_comment_container = document.createElement('div');
            nested_comment_container.dataset.id = `nested_comment_container_${id}`;
            nested_comment_container.style.display = 'none';

            const reply_input_element = document.querySelector(`[data-id=reply_input_${id}]`);
            reply_input_element.after(nested_comment_container);


        }

        function replay_count_render(id, comment_cnt, parent_element) {
            const nested_comment_view_div = document.createElement('div');
            nested_comment_view_div.classList.add('replay_count_view');
            nested_comment_view_div.dataset.id = `replay_count_view_${id}`;
            nested_comment_view_div.innerHTML = `댓글 ${comment_cnt}개`;
            nested_comment_view_div.onclick = toggle_nested_comment;
            parent_element.appendChild(nested_comment_view_div);
        }

        function load() {
            let url = `/main_board/get_main_board.php?main_board_id=${main_board_id}`;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    const {
                        main_data,
                        file_list,
                        comment_list
                    } = data;

                    for (comment_data of comment_list) {
                        comment_ui_add(comment_data, 'asc');
                    }

                    for (file_data of file_list) {
                        file_ui_add(file_data);
                    }


                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function file_ui_add(file_data) {
            const swiper_wrapper = document.querySelector('.swiper-wrapper');

            const swiper_slide = document.createElement('div');
            swiper_slide.classList.add('swiper-slide');

            const img = document.createElement('img');
            img.src = file_data.path;

            swiper_slide.appendChild(img);

            swiper_wrapper.appendChild(swiper_slide);

        }

        function toggle_reply_intput() {
            const id = this.dataset.id;

            const reply_element = document.querySelector(`[data-id = "reply_input_${id}"]`);
            const display = reply_element.style.display;
            if (display === 'none') {
                reply_element.style.display = "block"
            } else if (display === 'block') {
                reply_element.style.display = "none"
            }

        }

        function add_reply_input(id) {
            const main_view_reply_input_item_box = document.createElement('div');
            main_view_reply_input_item_box.classList.add('main_view_reply_input_item_box');
            main_view_reply_input_item_box.dataset.id = `reply_input_${id}`;
            main_view_reply_input_item_box.style.display = 'none';
            main_view_reply_input_item_box.style.backgroundColor = 'antiquewhite';

            const main_view_reply_input_item_flex_box = document.createElement('div');
            main_view_reply_input_item_flex_box.classList.add('main_view_reply_input_item_flex_box');

            const user_profile_content_box = document.createElement('div');
            user_profile_content_box.classList.add('user_profile_content_box');

            const profile_img = document.createElement('img');
            profile_img.src = session_profile_img;
            profile_img.classList.add('pointer');

            user_profile_content_box.appendChild(profile_img);

            main_view_reply_input_item_flex_box.appendChild(user_profile_content_box);

            const reply_input_box = document.createElement('div');
            reply_input_box.classList.add('width80_p');
            reply_input_box.classList.add('relative_box');

            const reply_input = document.createElement('input');
            reply_input.classList.add('plain_input');
            reply_input.classList.add('relative_box');
            reply_input.classList.add('relative_viertical');
            reply_input.dataset.id = `nested_comment_id_${id}`;
            reply_input.setAttribute('placeholder', '답글 추가...');

            reply_input_box.appendChild(reply_input);

            main_view_reply_input_item_flex_box.appendChild(reply_input_box);

            const button_box = document.createElement('div');
            button_box.classList.add('margin_top_7');
            button_box.classList.add('text-right');

            const cancle_span = document.createElement('span');
            cancle_span.classList.add('pointer');
            cancle_span.classList.add('bold');
            cancle_span.dataset.id = `${id}`;
            cancle_span.onclick = reply_input_cancel;
            cancle_span.innerHTML = "취소";

            const replay_span = document.createElement('span');
            replay_span.classList.add('pointer');
            replay_span.classList.add('margin_left_12');
            replay_span.dataset.id = `${id}`;
            replay_span.onclick = reply_input_post;
            replay_span.innerHTML = "답글";

            button_box.appendChild(cancle_span);
            button_box.appendChild(replay_span);

            main_view_reply_input_item_box.appendChild(main_view_reply_input_item_flex_box);
            main_view_reply_input_item_box.appendChild(button_box);

            const target = document.querySelector(`[data-id = "main_view_comment_item_box_${id}"`);
            target.after(main_view_reply_input_item_box);

        }

        function add_nested_comment(data) {
            const {
                id,
                parent_id,
                comment,
                nick_name,
                create_user,
                create_date
            } = data;

            let target = document.querySelector(`[data-id = "nested_comment_container_${parent_id}"`);

            const nested_comment_box = document.createElement('div');
            nested_comment_box.classList.add('nested_comment_box');
            nested_comment_box.style.paddingLeft = '55px';

            const user_profile_content_box = document.createElement('div');
            user_profile_content_box.classList.add('user_profile_content_box');

            const profile_img = document.createElement('img');
            profile_img.src = session_profile_img;
            profile_img.classList.add('pointer');


            user_profile_content_box.appendChild(profile_img);

            nested_comment_box.appendChild(user_profile_content_box);

            const main_view_comment_item_content_box = document.createElement('div');
            main_view_comment_item_content_box.classList.add('main_view_comment_item_content_box');
            main_view_comment_item_content_box.classList.add('relative_box');
            main_view_comment_item_content_box.dataset.id = `main_view_comment_item_content_box_${id}`;


            const nick_name_container = document.createElement('div');
            nick_name_container.classList.add('bold');
            if (is_empty(nick_name)) {
                nick_name_container.innerHTML = session_nick_name;
            } else {
                nick_name_container.innerHTML = nick_name;
            }

            const comment_container = document.createElement('div');
            comment_container.classList.add('black');
            comment_container.classList.add('word_break');
            comment_container.innerHTML = comment;

            const comment_add_info_container = document.createElement('div');

            const time_span = document.createElement('span');
            time_span.classList.add('gray');
            time_span.innerHTML = create_date;

            const nested_comment_post_div = document.createElement('div');
            nested_comment_post_div.classList.add('gray');
            nested_comment_post_div.classList.add('bold');
            nested_comment_post_div.classList.add('pointer');
            nested_comment_post_div.dataset.id = id;
            nested_comment_post_div.onclick = toggle_reply_intput;
            nested_comment_post_div.innerHTML = "댓글달기";

            comment_add_info_container.appendChild(time_span);
            comment_add_info_container.appendChild(nested_comment_post_div);

            main_view_comment_item_content_box.appendChild(nick_name_container);
            main_view_comment_item_content_box.appendChild(comment_container);
            main_view_comment_item_content_box.appendChild(comment_add_info_container);

            nested_comment_box.appendChild(main_view_comment_item_content_box);

            target.appendChild(nested_comment_box);


            // const main_view_reply_input_item_box = document.createElement('div');
            // main_view_reply_input_item_box.classList.add('main_view_reply_input_item_box');
            // main_view_reply_input_item_box.dataset.id = `reply_input_${id}`;
            // // main_view_reply_input_item_box.style.display = 'none';

            // const main_view_reply_input_item_flex_box = document.createElement('div');
            // main_view_reply_input_item_flex_box.classList.add('main_view_reply_input_item_flex_box');

            // const user_profile_content_box = document.createElement('div');
            // user_profile_content_box.classList.add('user_profile_content_box');

            // const profile_img = document.createElement('img');
            // profile_img.src = session_profile_img;
            // profile_img.classList.add('pointer');

            // user_profile_content_box.appendChild(profile_img);

            // main_view_reply_input_item_flex_box.appendChild(user_profile_content_box);

            // const reply_input_box = document.createElement('div');
            // reply_input_box.classList.add('width80_p');
            // reply_input_box.classList.add('relative_box');

            // const reply_input = document.createElement('input');
            // reply_input.classList.add('plain_input');
            // reply_input.classList.add('relative_box');
            // reply_input.classList.add('relative_viertical');
            // reply_input.dataset.id = `nested_comment_id_${id}`
            // reply_input.value = data.comment;
            // reply_input.setAttribute('placeholder', '답글 수정...');

            // reply_input_box.appendChild(reply_input);

            // main_view_reply_input_item_flex_box.appendChild(reply_input_box);

            // const button_box = document.createElement('div');
            // button_box.classList.add('margin_top_7');
            // button_box.classList.add('text-right');

            // const cancle_span = document.createElement('span');
            // cancle_span.classList.add('pointer');
            // cancle_span.classList.add('bold');
            // cancle_span.dataset.id = `${id}`;
            // cancle_span.onclick = reply_input_cancel;
            // cancle_span.innerHTML = "취소";

            // const replay_span = document.createElement('span');
            // replay_span.classList.add('pointer');
            // replay_span.classList.add('margin_left_12');
            // replay_span.dataset.id = `${id}`;
            // replay_span.onclick = reply_input_post;
            // replay_span.innerHTML = "수정";

            // button_box.appendChild(cancle_span);
            // button_box.appendChild(replay_span);

            // main_view_reply_input_item_box.appendChild(main_view_reply_input_item_flex_box);
            // main_view_reply_input_item_box.appendChild(button_box);

            // const target = document.querySelector(`[data-id = "nested_comment_container_${id}"`);
            // target.appendChild(main_view_reply_input_item_box);

        }

        function toggle_nested_comment() {
            const parent_id = Number(this.dataset.id.split('_')[3]);
            const target = document.querySelector(`[data-id = "nested_comment_container_${parent_id}"]`);
            const display = target.style.display;
            if (display === 'none') {
                target.style.display = "block"
            } else if (display === 'block') {
                target.style.display = "none"
            }

            const childern = target.children;
            if (childern.length == 0) {
                $.ajax({
                    url: `get_main_board_nested_comment.php?main_board_id=${main_board_id}&parent_id=${parent_id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        if (result.length > 0) {
                            for (let data of result) {
                                add_nested_comment(data);
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            }

        }

        function nested_comment() {

        }

        function reply_input_cancel() {
            const id = this.dataset.id;
            console.log('id:', id);
            const reply_element = document.querySelector(`[data-id = "reply_input_${id}"]`);
            if (!is_empty(reply_element)) {
                reply_element.style.display = "none"
            }
        }

        function reply_input_post() {
            const id = this.dataset.id;
            console.log('id:', id);

            const input_element = document.querySelector(`[data-id="nested_comment_id_${id}"]`);
            const value = input_element.value;

            $.ajax({
                url: 'post_create_nested_comment.php',
                type: 'POST',
                data: {
                    nested_comment: value,
                    main_board_id,
                    parent_id: id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.result) {
                        const {
                            id
                            , parent_id
                            , comment
                            , nick_name
                        } = result.data[0];

                        const replay_count_view = document.querySelector(`[data-id=replay_count_view_${parent_id}]`);
                        const main_view_comment_item_content_box = document.querySelector(`[data-id=main_view_comment_item_content_box_${parent_id}]`);

                        input_element.value = "";

                        if (is_empty(replay_count_view)) {
                            replay_count_render(parent_id, 1, main_view_comment_item_content_box);
                        } else {
                            const before_cnt = replay_count_view.innerHTML.split(" ")[1].slice(0, -1);
                            let cnt = Number(before_cnt) + 1;
                            replay_count_view.innerHTML = `댓글 ${cnt}개`;

                            // 댓글창이 1번이라도 열렸을 경우 이곳에서 렌더링
                            const nested_comment_container = document.querySelector(`[data-id = "nested_comment_container_${parent_id}"`);
                            if (nested_comment_container.children.length > 0) {
                                add_nested_comment(result.data[0]);
                            }

                        }

                    }

                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }
    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="free_board_section layout_padding create_free_board">
        <div class="container">
            <div class="main_view_flex_container relative_box">
                <div class="main_view_img_box relative_box mySwiper">
                    <div class="swiper-wrapper">
                        <!-- <div class="swiper-slide"><img src="/images/default_img.png"></div>
                        <div class="swiper-slide"><img src="/images/default_img.png"></div> -->
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="main_view_comment_box">

                    <div class="main_view_comment_header_box relative_box">
                        <div class="user_profile_header_box relative_box relative_viertical">
                            <img class="pointer " src="<?php echo $_SESSION['profile_img'] ?>">
                        </div>
                        <div class="relative_box">
                            <div class="main_view_header_nick_name margin_left bold">너구리</div>
                        </div>
                    </div>

                    <div id="comment_content_box" class="main_view_comment_content_box">

                        <div class="main_view_comment_item_box relative_box">
                            <div class="user_profile_content_box ">
                                <img class="pointer" src="/download/profile/phpinfo 이미지_20230630_222755.png">
                            </div>
                            <div class="main_view_comment_item_content_box relative_box">
                                <div class="bold">너구리</div>
                                <div class="black word_break">텍스트</div>
                                <div class="">
                                    <span class="gray">10분</span>
                                    <span class="gray pointer bold margin_left_12">댓글달기</span>
                                </div>
                            </div>
                            <div class="add_info_container">
                            </div>
                        </div>

                        <div class="main_view_reply_input_item_box">
                            <div class="main_view_reply_input_item_flex_box">
                                <div class="user_profile_content_box">
                                    <img class="pointer" src="/download/profile/phpinfo 이미지_20230630_222755.png">
                                </div>
                                <div class="width80_p relative_box">
                                    <input class="plain_input relative_box relative_viertical" placeholder="답글 추가..." />
                                </div>
                            </div>
                            <div class="margin_top_7 text-right">
                                <span class="pointer bold">취소</span>
                                <span class="pointer margin_left_12">답글</span>
                            </div>
                        </div>

                    </div>

                    <div class="main_view_comment_floor_box relative_box">
                        <input id="comment_input" class="plain_input relative_viertical relative_box" style="width:80%;"
                            placeholder="댓글 달기..." />
                        <div class="relative_box pointer">
                            <div id="post" class="relative_box relative_viertical">개시</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 제목및 버튼 컨테이너 -->
            <!-- <div class="create_container">
                <div class="board_detail_title" style="width:50%; flex-grow: 1;">게시글 뷰</div>
                <div style="flex-grow: 999;"></div>
                <div style="flex-grow: 1;">
                    <button type="button" class="btn btn-white" id="cancle_btn">취소</button>
                    <button type="button" class="btn btn-dark" id="update_btn">수정</button>
                </div>
            </div>
            <hr> -->
            <!-- 파일 리스트 보여주는 컨테이너 -->
            <!-- <div id="main_view_grid_container" class="main_view_grid_container">
                <div class="main_grid_file" data-uuid="eb235a58-32b9-415d-8a7a-11c5d6aaf60d">
                    <img class="file-img" src="/download/main_board_img/delete_guide1_change_20230701_134230.jpg">
                    <div class="file_txt">delete...ge.jpg</div>
                </div>
                <div class="main_grid_file" data-uuid="eb235a58-32b9-415d-8a7a-11c5d6aaf60d">
                    <img class="file-img" src="/download/main_board_img/delete_guide1_change_20230701_134230.jpg">
                    <div class="file_txt">delete...ge.jpg</div>
                </div>
                <div class="main_grid_file" data-uuid="eb235a58-32b9-415d-8a7a-11c5d6aaf60d">
                    <img class="file-img" src="/download/main_board_img/delete_guide1_change_20230701_134230.jpg">
                    <div class="file_txt">delete...ge.jpg</div>
                </div>
                <div class="main_grid_file" data-uuid="eb235a58-32b9-415d-8a7a-11c5d6aaf60d">
                    <img class="file-img" src="/download/main_board_img/delete_guide1_change_20230701_134230.jpg">
                    <div class="file_txt">delete...ge.jpg</div>
                </div>
                <div class="main_grid_file" data-uuid="eb235a58-32b9-415d-8a7a-11c5d6aaf60d">
                    <img class="file-img" src="/download/main_board_img/delete_guide1_change_20230701_134230.jpg">
                    <div class="file_txt">delete...ge.jpg</div>
                </div>
            </div> -->
        </div>
    </div>


    <?php require '../modal/file_main_board_modal.php'; ?>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>

</body>

</html>