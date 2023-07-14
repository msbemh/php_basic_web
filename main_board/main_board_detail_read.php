<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>

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
            // 처음 이미지, 댓글, 메모 정보 로드하여 UI 렌더링
            load();

            $('#delete_btn').on('click', function () {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-dark',
                        cancelButton: 'btn btn-white'
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: '삭제',
                    text: '정말 삭제 하시겠습니까?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '확인',
                    cancelButtonText: '취소',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'post_delete_main_board.php',
                            type: 'POST',
                            data: {
                                main_board_id
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.result) {
                                    window.location.href = `/index.php`;
                                } else {
                                    Swal.fire(
                                        '결과',
                                        '삭제실패',
                                        'fail'
                                    );
                                }
                            },
                            error: function (xhr, status, error) {
                                console.log(error);
                            }
                        });
                    }
                });
            });

            $('#update_btn').on('click', function () {
                window.location.href = `main_board_detail_update.php?main_board_id=${main_board_id}`;
            });

            // 일반 댓글 저장
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
                                create_profile_img,
                                nick_name,
                                create_user,
                                create_date
                            } = data;

                            // 일반 댓글 UI 추가
                            comment_ui_add(data);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    },
                    complete: function () {
                    }
                });

            });

            // 댓글 수정/삭제 팝업 영역 바깥을 클릭시, 팝업창 닫히게 하는 이벤트
            document.addEventListener('mouseup', function (e) {
                const elements = document.querySelectorAll(`.drop_down_popup[style*="display: block"]`);

                if (is_empty(elements) || elements.length < 1) return;

                for (const element of elements) {
                    if ($(element).has(e.target).length === 0) {
                        element.style.display = 'none';
                    }
                }

            });
        });

        // 평범한 댓글(대댓글x) UI 렌더링
        function comment_ui_add(data) {

            const {
                id,
                parent_id,
                comment,
                nick_name,
                create_profile_img,
                create_user,
                create_date,
                comment_cnt
            } = data;

            // 댓글 item 1개를 표시하는 최상위 컨테이너
            let target = document.createElement('div');
            target.classList.add('main_view_comment_item_box');
            target.classList.add('relative_box');
            target.dataset.id = `main_view_comment_item_box_${id}`;

            // 사용자 프로필 이미지 부분
            const user_profile_content_box = document.createElement('div');
            user_profile_content_box.classList.add('user_profile_content_box');

            const profile_img = document.createElement('img');
            if (create_profile_img) {
                profile_img.src = create_profile_img;
            } else {
                profile_img.src = default_profile_img;
            }

            profile_img.classList.add('pointer');

            user_profile_content_box.appendChild(profile_img);
            target.appendChild(user_profile_content_box);

            // 닉네임, 컨텐츠, 날짜, '댓글달기'를 위한 컨테이너
            const main_view_comment_item_content_box = document.createElement('div');
            main_view_comment_item_content_box.classList.add('main_view_comment_item_content_box');
            main_view_comment_item_content_box.classList.add('relative_box');
            main_view_comment_item_content_box.dataset.id = `main_view_comment_item_content_box_${id}`;

            // 닉네임 부분
            const nick_name_container = document.createElement('div');
            nick_name_container.classList.add('bold');
            if (is_empty(nick_name)) {
                nick_name_container.innerHTML = session_nick_name;
            } else {
                nick_name_container.innerHTML = nick_name;
            }

            // 댓글 내용 텍스트 부분
            const comment_text = document.createElement('div');
            comment_text.classList.add('black');
            comment_text.classList.add('word_break');
            comment_text.innerHTML = comment;
            comment_text.dataset.id = `comment_txt_${id}`;
            comment_text.style.display = 'block';

            // 댓글 수정을 위한 부분
            const comment_update_container = document.createElement('div');
            comment_update_container.dataset.id = `comment_update_container_${id}`;
            comment_update_container.style.display = 'none';

            const comment_input = document.createElement('input');
            comment_input.classList.add('black');
            comment_input.classList.add('word_break');
            comment_input.classList.add('plain_input');
            comment_input.value = comment;
            comment_input.dataset.id = `comment_input_${id}`;
            comment_input.style.display = 'block';

            const comment_update_btn = document.createElement('span');
            comment_update_btn.classList.add('pointer');
            comment_update_btn.innerHTML = '취소';
            comment_update_btn.dataset.id = `${id}`;
            comment_update_btn.style.float = 'right';
            comment_update_btn.onclick = cancel_comment_update;

            const comment_cancel_btn = document.createElement('span');
            comment_cancel_btn.classList.add('pointer');
            comment_cancel_btn.classList.add('margin_left');
            comment_cancel_btn.innerHTML = '수정';
            comment_cancel_btn.dataset.id = `${id}`;
            comment_cancel_btn.style.float = 'right';
            comment_cancel_btn.onclick = comment_update_post;

            comment_update_container.appendChild(comment_input);
            comment_update_container.appendChild(comment_cancel_btn);
            comment_update_container.appendChild(comment_update_btn);

            // 댓글 날짜와 '댓글달기' 버튼을 위한 부분
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
            nested_comment_post_span.onclick = toggle_reply_input;
            nested_comment_post_span.innerHTML = "댓글달기";

            comment_add_info_container.appendChild(time_span);
            comment_add_info_container.appendChild(nested_comment_post_span);

            main_view_comment_item_content_box.appendChild(nick_name_container);
            main_view_comment_item_content_box.appendChild(comment_text);
            main_view_comment_item_content_box.appendChild(comment_update_container);
            main_view_comment_item_content_box.appendChild(comment_add_info_container);

            // 대댓글이 존재할 경우 표시
            if (comment_cnt > 0) {
                replay_count_render(id, comment_cnt, main_view_comment_item_content_box);
            }

            /**
             * 댓글 작성자만 수정 삭제 가능한 부분 표시
             * 추가정보(수정/삭제) 이미지 추가
             */
            if (create_user === session_email) {
                add_add_info_img(id, target, parent_id);
            }

            target.appendChild(main_view_comment_item_content_box);

            comment_content_box.prepend(target);

            // 댓글달기 input 부분
            add_reply_input(id);

            // 대댓글 영역 틀만 미리 구현
            const nested_comment_container = document.createElement('div');
            nested_comment_container.dataset.id = `nested_comment_container_${id}`;
            nested_comment_container.style.display = 'none';

            const reply_input_element = document.querySelector(`[data-id=reply_input_${id}]`);
            reply_input_element.after(nested_comment_container);

        }

        // 댓글 개수 표시 
        function replay_count_render(id, comment_cnt, parent_element) {
            const nested_comment_view_div = document.createElement('div');
            nested_comment_view_div.classList.add('replay_count_view');
            nested_comment_view_div.dataset.id = `replay_count_view_${id}`;
            nested_comment_view_div.innerHTML = `댓글 ${comment_cnt}개`;
            nested_comment_view_div.onclick = toggle_nested_comment;
            parent_element.appendChild(nested_comment_view_div);
        }

        // 초기 데이터 로드
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
                        comment_ui_add(comment_data);
                    }

                    for (file_data of file_list) {
                        file_ui_add(file_data);
                    }

                    const create_user = main_data[0].create_user;
                    const create_user_nick_name = main_data[0].nick_name;
                    const create_profile_img = main_data[0].create_profile_img;

                    const create_profile_img_element = document.querySelector('.create_profile_img');
                    if(is_empty(create_profile_img)){
                        create_profile_img_element.src = default_profile_img;
                    }else{
                        create_profile_img_element.src = create_profile_img;
                    }
                    

                    const create_user_nick_name_element = document.querySelector('.main_view_header_nick_name');
                    create_user_nick_name_element.innerHTML = create_user_nick_name;

                    const update_btn = document.querySelector('#update_btn');
                    const delete_btn = document.querySelector('#delete_btn');
                    if (create_user === session_email) {
                        update_btn.style.display = 'inline-block';
                        delete_btn.style.display = 'inline-block';
                    } else {
                        update_btn.style.display = 'none';
                        delete_btn.style.display = 'none';
                    }


                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

        // 파일 ui 렌더링
        function file_ui_add(file_data) {
            const swiper_wrapper = document.querySelector('.swiper-wrapper');

            const swiper_slide = document.createElement('div');
            swiper_slide.classList.add('swiper-slide');

            const img = document.createElement('img');
            img.src = file_data.path;

            swiper_slide.appendChild(img);

            swiper_wrapper.appendChild(swiper_slide);

        }

        // 대댓글 달기위한 대댓글 입력창 toggle
        function toggle_reply_input() {
            const id = this.dataset.id;

            const reply_element = document.querySelector(`[data-id = "reply_input_${id}"]`);
            const display = reply_element.style.display;
            if (display === 'none') {
                reply_element.style.display = "block"
            } else if (display === 'block') {
                reply_element.style.display = "none"
            }

        }

        // 대댓글 입력창 렌더링
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
            profile_img.classList.add('pointer');
            if(is_empty(session_profile_img)){
                profile_img.src = default_profile_img;
            }else{
                profile_img.src = session_profile_img;
            }

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

        // 대댓글 UI 렌더링
        function add_nested_comment(data) {
            const {
                id,
                parent_id,
                comment,
                nick_name,
                create_profile_img,
                create_user,
                create_date
            } = data;

            let target = document.querySelector(`[data-id = "nested_comment_container_${parent_id}"`);

            const nested_comment_box = document.createElement('div');
            nested_comment_box.classList.add('nested_comment_box');
            nested_comment_box.classList.add('relative_box');
            nested_comment_box.style.paddingLeft = '55px';
            nested_comment_box.dataset.id = `nested_comment_box_${id}`;

            const user_profile_content_box = document.createElement('div');
            user_profile_content_box.classList.add('user_profile_content_box');

            const profile_img = document.createElement('img');
            if (create_profile_img) {
                profile_img.src = create_profile_img;
            } else {
                profile_img.src = default_profile_img;
            }
            profile_img.classList.add('pointer');


            user_profile_content_box.appendChild(profile_img);

            nested_comment_box.appendChild(user_profile_content_box);

            // 추가정보(수정/삭제) 이미지 추가
            if (create_user === session_email) {
                add_add_info_img(id, nested_comment_box, parent_id);
            }

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

            // 댓글 내용 텍스트 부분
            const comment_text = document.createElement('div');
            comment_text.classList.add('black');
            comment_text.classList.add('word_break');
            comment_text.innerHTML = comment;
            comment_text.dataset.id = `comment_txt_${id}`;
            comment_text.style.display = 'block';

            // 댓글 수정을 위한 부분
            const comment_update_container = document.createElement('div');
            comment_update_container.dataset.id = `comment_update_container_${id}`;
            comment_update_container.style.display = 'none';

            const comment_input = document.createElement('input');
            comment_input.classList.add('black');
            comment_input.classList.add('word_break');
            comment_input.classList.add('plain_input');
            comment_input.value = comment;
            comment_input.dataset.id = `comment_input_${id}`;
            comment_input.style.display = 'block';

            const comment_update_btn = document.createElement('span');
            comment_update_btn.classList.add('pointer');
            comment_update_btn.innerHTML = '취소';
            comment_update_btn.dataset.id = `${id}`;
            comment_update_btn.style.float = 'right';
            comment_update_btn.onclick = cancel_comment_update;

            const comment_cancel_btn = document.createElement('span');
            comment_cancel_btn.classList.add('pointer');
            comment_cancel_btn.classList.add('margin_left');
            comment_cancel_btn.innerHTML = '수정';
            comment_cancel_btn.dataset.id = `${id}`;
            comment_cancel_btn.style.float = 'right';
            comment_cancel_btn.onclick = comment_update_post;

            comment_update_container.appendChild(comment_input);
            comment_update_container.appendChild(comment_cancel_btn);
            comment_update_container.appendChild(comment_update_btn);


            // '댓글 달기' 버튼 영역
            const comment_add_info_container = document.createElement('div');
            const time_span = document.createElement('span');
            time_span.classList.add('gray');
            time_span.innerHTML = create_date;

            const nested_comment_post_div = document.createElement('div');
            nested_comment_post_div.classList.add('gray');
            nested_comment_post_div.classList.add('bold');
            nested_comment_post_div.classList.add('pointer');
            nested_comment_post_div.dataset.id = id;
            nested_comment_post_div.onclick = toggle_reply_input;
            nested_comment_post_div.innerHTML = "댓글달기";

            comment_add_info_container.appendChild(time_span);
            comment_add_info_container.appendChild(nested_comment_post_div);

            main_view_comment_item_content_box.appendChild(nick_name_container);
            main_view_comment_item_content_box.appendChild(comment_text);
            main_view_comment_item_content_box.appendChild(comment_update_container);
            main_view_comment_item_content_box.appendChild(comment_add_info_container);

            nested_comment_box.appendChild(main_view_comment_item_content_box);

            target.appendChild(nested_comment_box);
        }

        // 수정/삭제를 위한 img 렌더링
        function add_add_info_img(id, target, parent_id) {
            const dropdown_container = document.createElement('div');
            dropdown_container.classList.add('add_info_container');
            dropdown_container.dataset.id = id;

            const add_info_img = document.createElement('div');
            add_info_img.classList.add('add_info_img');
            add_info_img.setAttribute('aria-haspopup', true);
            add_info_img.setAttribute('aria-expanded', false);
            add_info_img.dataset.id = id;
            add_info_img.onclick = toggle_add_info;

            const drop_menu_container = document.createElement('div');
            drop_menu_container.classList.add('dropdown-menu');
            drop_menu_container.classList.add('drop_down_popup');
            drop_menu_container.dataset.id = `dropdown_menu_${id}`;

            const dropdown_update_item = document.createElement('a');
            dropdown_update_item.classList.add('dropdown-item');
            dropdown_update_item.innerHTML = '수정';
            dropdown_update_item.dataset.id = id;
            dropdown_update_item.onclick = show_comment_update;

            const dropdown_delete_item = document.createElement('a');
            dropdown_delete_item.classList.add('dropdown-item');
            dropdown_delete_item.innerHTML = '삭제';
            dropdown_delete_item.dataset.id = id;
            dropdown_delete_item.dataset.parent_id = parent_id;
            dropdown_delete_item.onclick = comment_delete;

            drop_menu_container.appendChild(dropdown_update_item);
            drop_menu_container.appendChild(dropdown_delete_item);

            dropdown_container.appendChild(add_info_img);
            dropdown_container.appendChild(drop_menu_container);

            target.appendChild(dropdown_container);
        }

        // 수정/삭제를 위한 팝업창 토글
        function toggle_add_info() {
            const id = this.dataset.id;
            const drop_menu_element = document.querySelector(`[data-id="dropdown_menu_${id}"]`);

            if (drop_menu_element.style.display === 'none' || is_empty(drop_menu_element.style.display)) {
                drop_menu_element.style.display = 'block';
            } else if (drop_menu_element.style.display === 'block') {
                drop_menu_element.style.display = 'none';
            }
        }

        // 대댓글 창 토글
        function toggle_nested_comment() {
            const parent_id = Number(this.dataset.id.split('_')[3]);
            const target = document.querySelector(`[data-id = "nested_comment_container_${parent_id}"]`);
            const display = target.style.display;

            // 대댓글 창 토글
            if (display === 'none') {
                target.style.display = "block"
            } else if (display === 'block') {
                target.style.display = "none"
            }

            // 처음 대댓글창을 열게 되면 데이터를 서버에서 받아온다
            const childern = target.children;
            if (childern.length == 0) {
                $.ajax({
                    url: `get_main_board_nested_comment.php?main_board_id=${main_board_id}&parent_id=${parent_id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        if (result.length > 0) {
                            // 대댓글 UI 렌더링
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

        function cancel_comment_update() {
            const id = this.dataset.id;

            none_comment_update(id);

            const element_comment_text = document.querySelector(`[data-id=comment_txt_${id}]`);
            const element_comment_input = document.querySelector(`[data-id=comment_input_${id}]`);

            element_comment_input.value = element_comment_text.innerHTML;

        }

        // 일반 댓글 수정 영역 show
        function show_comment_update(id) {
            if (isNaN(id)) {
                id = this.dataset.id;
            }

            // 댓글의 수정영역 show
            const element_comment_update = document.querySelector(`[data-id=comment_update_container_${id}]`);
            element_comment_update.style.display = 'block';

            // 댓글의 텍스트 영역 No show
            const element_comment_text = document.querySelector(`[data-id=comment_txt_${id}]`);
            element_comment_text.style.display = 'none';

            // 수정 삭제 팝업창 No show
            const element_pop_up = document.querySelector(`.drop_down_popup`);
            element_pop_up.style.display = 'none';
        }

        // 댓글/대댓글 수정 영역 안보이게
        function none_comment_update(id) {
            if (isNaN(id)) {
                id = this.dataset.id;
            }

            // 댓글의 수정영역 No show
            const element_comment_update = document.querySelector(`[data-id=comment_update_container_${id}]`);
            element_comment_update.style.display = 'none';

            // 댓글의 텍스트 영역 show
            const element_comment_text = document.querySelector(`[data-id=comment_txt_${id}]`);
            element_comment_text.style.display = 'block';
        }

        // 댓글/대댓글 삭제
        function comment_delete() {
            const id = this.dataset.id;
            const parent_id = this.dataset.parent_id;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-dark',
                    cancelButton: 'btn btn-white'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: '삭제',
                text: '정말 삭제 하시겠습니까?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '확인',
                cancelButtonText: '취소',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_comment(id, parent_id);
                }
            });

            this.parentElement.style.display = 'none';
            // const element = document.querySelector(`.drop_down_popup`);
            // element.style.display = 'none';
        }

        function delete_comment(id, parent_id) {
            $.ajax({
                url: 'post_delete_comment.php',
                type: 'POST',
                data: {
                    id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.result) {
                        // UI 삭제
                        Swal.fire(
                            '결과',
                            '삭제완료',
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                // 댓글 데이터 존재한다면 삭제
                                const element1 = document.querySelector(`[data-id="main_view_comment_item_box_${id}"]`);
                                if (!is_empty(element1)) element1.remove();

                                const element2 = document.querySelector(`[data-id="reply_input_${id}"]`);
                                if (!is_empty(element2)) element2.remove();

                                const element3 = document.querySelector(`[data-id="nested_comment_container_${id}"]`);
                                if (!is_empty(element3)) element3.remove();

                                const reply_element = document.querySelector(`[data-id="nested_comment_box_${id}"]`);
                                if (!is_empty(reply_element)) reply_element.remove();

                                const replay_count_view = document.querySelector(`[data-id=replay_count_view_${parent_id}]`);
                                render_reply_count(replay_count_view, 'minus');

                            }
                        });
                    } else {
                        Swal.fire(
                            '결과',
                            '삭제실패',
                            'fail'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

        // 대댓글 취소
        function reply_input_cancel() {
            const id = this.dataset.id;
            console.log('id:', id);
            const reply_element = document.querySelector(`[data-id = "reply_input_${id}"]`);
            if (!is_empty(reply_element)) {
                reply_element.style.display = "none"
            }
        }

        // 댓글/대댓글 수정
        function comment_update_post() {
            const id = this.dataset.id;
            const input_element = document.querySelector(`[data-id=comment_input_${id}]`);
            const element_comment_text = document.querySelector(`[data-id=comment_txt_${id}]`);
            const comment_value = input_element.value;

            $.ajax({
                url: 'post_update_comment.php',
                type: 'POST',
                data: {
                    comment: comment_value,
                    id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.result) {
                        element_comment_text.innerHTML = comment_value;
                        // 댓글 수정 영역 숨기기
                        none_comment_update(id);
                    }

                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function render_reply_count(target, type) {
            const before_cnt = target.innerHTML.split(" ")[1].slice(0, -1);
            let cnt = Number(before_cnt);
            if (type === 'plus') {
                cnt++;
            } else if (type === 'minus') {
                cnt--;
            }
            target.innerHTML = `댓글 ${cnt}개`;
        }

        // 대댓글 입력 post
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
                            , create_profile_img
                        } = result.data[0];

                        const replay_count_view = document.querySelector(`[data-id=replay_count_view_${parent_id}]`);
                        const main_view_comment_item_content_box = document.querySelector(`[data-id=main_view_comment_item_content_box_${parent_id}]`);

                        input_element.value = "";

                        if (is_empty(replay_count_view)) {
                            replay_count_render(parent_id, 1, main_view_comment_item_content_box);
                        } else {
                            // 댓글/대댓글 숫자 변경
                            render_reply_count(replay_count_view, 'plus');

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
            <div class="main_board_read_button_container">
                <button type="button" style="display:none;" class="btn btn-dark" id="delete_btn">삭제</button>
                <button type="button" style="display:none;" class="btn btn-dark" id="update_btn">수정</button>
            </div>
            <div class="main_view_flex_container">
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
                            <img class="pointer create_profile_img">
                        </div>
                        <div class="relative_box">
                            <div class="main_view_header_nick_name margin_left bold">
                            </div>
                        </div>
                    </div>

                    <div id="comment_content_box" class="main_view_comment_content_box">

                        <!-- <div class="main_view_comment_item_box">
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
                        </div>

                        <div class="main_view_reply_input_item_box" >
                            <div class="main_view_reply_input_item_flex_box">
                                <div class="user_profile_content_box">
                                    <img class="pointer" src="/download/profile/phpinfo 이미지_20230630_222755.png">
                                </div>
                                <div class="width80_p relative_box">
                                    <input class="plain_input relative_box relative_viertical" placeholder="답글 추가..."/>
                                </div>
                            </div>
                            <div class="margin_top_7 text-right">
                                <span class="pointer bold">취소</span>
                                <span class="pointer margin_left_12">답글</span>
                            </div>
                        </div> -->

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