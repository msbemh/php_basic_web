<!DOCTYPE html>
<html lang="en">

<head>
   <?php require './head.php'; ?>
</head>
<script>
   $(document).on('ready', function () {
      // init();

   });

   function init() {
      $.ajax({
         url: `main_board/get_main_board_all.php`,
         type: 'GET',
         dataType: 'json',
         success: function (datas) {

            const final_data = make_data_structure(datas);

            for (const data of final_data) {
               card_ui_add(data);
            }

            // 동적으로 생성한 html에 swiper가 동작되지 않아 다시 생성하고 update 시켜줬다
            swiper = new Swiper(".mySwiper", {
               navigation: {
                  nextEl: ".swiper-button-next",
                  prevEl: ".swiper-button-prev",
               },
               observer: true,
               observeParents: true
            });

         },
         error: function (xhr, status, error) {
            console.log(error);
         }
      });
   }

   function make_data_structure(datas) {
      const final_data = [];
      
      for (const data of datas) {
         const {
            id,
            memo,
            main_board_id,
            path,
            create_user,
            nick_name,
            profile_img,
            create_date,
            is_heart_click,
            heart_cnt,
            is_following
         } = data;

         if (final_data.length != 0) {
            const length = final_data.length;
            const last_obj = final_data[length - 1];

            if (id === last_obj.id) {
               last_obj.file_list.push({
                  path
               });
            } else {
               const obj = {
                  id,
                  memo,
                  create_user,
                  create_date,
                  is_heart_click,
                  heart_cnt,
                  nick_name,
                  profile_img,
                  is_following,
                  file_list: []
               };

               if (path) obj.file_list.push({ path });

               final_data.push(obj);
            }
         } else {
            const obj = {
               id,
               memo,
               create_user,
               create_date,
               is_heart_click,
               heart_cnt,
               nick_name,
               profile_img,
               is_following,
               file_list: []
            };

            if (path) obj.file_list.push({ path });

            final_data.push(obj);
         }

      }

      return final_data;

   }

   function card_ui_add(data) {

      const id = data.id;

      card_item_render(data);

      for (const file_data of data.file_list) {
         file_ui_add(file_data, id);
      }
   }

   function card_item_render(data) {
      const id = data.id;
      const home_container2 = document.querySelector('.home_container2');
      const is_following = data.is_following;
      const create_user = data.create_user;

      const card_item = document.createElement('div');
      card_item.classList.add('card_item');

      const card_header = document.createElement('div');
      card_header.classList.add('card_header');
      card_header.classList.add('relative_box');

      const WriterInfo = document.createElement('div');
      WriterInfo.classList.add('WriterInfo');
      WriterInfo.classList.add('pointer');
      WriterInfo.classList.add('left');
      WriterInfo.classList.add('relative_viertical');
      WriterInfo.classList.add('margin_left_30');

      const user_profile_header_box = document.createElement('div');
      user_profile_header_box.classList.add('user_profile_header_box');

      const profile_img = document.createElement('img');
      profile_img.classList.add('pointer');
      if (is_empty(data.profile_img)) {
         profile_img.src = default_profile_img;
      } else {
         profile_img.src = data.profile_img;
      }


      user_profile_header_box.appendChild(profile_img);
      WriterInfo.appendChild(user_profile_header_box);
      card_header.appendChild(WriterInfo);

      const nick_name_div = document.createElement('div');
      nick_name_div.classList.add('left');
      nick_name_div.classList.add('relative_box');
      nick_name_div.classList.add('relative_viertical');
      nick_name_div.classList.add('margin_left');
      nick_name_div.innerHTML = data.nick_name;

      card_header.appendChild(nick_name_div);

      const create_date_div = document.createElement('div');
      create_date_div.classList.add('left');
      create_date_div.classList.add('relative_box');
      create_date_div.classList.add('relative_viertical');
      create_date_div.classList.add('margin_left');
      create_date_div.innerHTML = data.create_date;

      card_header.appendChild(create_date_div);

      const follow_div = document.createElement('div');
      follow_div.classList.add('left');
      follow_div.classList.add('relative_box');
      follow_div.classList.add('relative_viertical');
      follow_div.classList.add('margin_left');
      follow_div.classList.add('pointer');
      follow_div.classList.add('follow');
      if (is_empty(is_following)) {
         if (session_email != create_user) {
            follow_div.innerHTML = `팔로우`;
         }
      } else {
         follow_div.innerHTML = `팔로잉`;
      }
      follow_div.dataset.id = `follow_${id}`;
      follow_div.dataset.create_user = `${data.create_user}`;
      follow_div.onclick = follow_event;

      card_header.appendChild(follow_div);

      const more_img_div = document.createElement('div');
      more_img_div.classList.add('right');
      more_img_div.classList.add('relative_box');
      more_img_div.classList.add('relative_viertical');
      more_img_div.classList.add('pointer');
      more_img_div.classList.add('more_img');
      more_img_div.classList.add('margin_right_20');

      card_header.appendChild(more_img_div);

      card_item.appendChild(card_header);

      // 사진들
      const card_body_div = document.createElement('div');
      card_body_div.classList.add('card_body');
      card_body_div.classList.add('relative_box');
      card_body_div.classList.add('mySwiper');

      card_item.appendChild(card_body_div);

      const swiper_wrapper_div = document.createElement('div');
      swiper_wrapper_div.classList.add('main_swiper_wrapper');
      swiper_wrapper_div.classList.add('swiper-wrapper');
      swiper_wrapper_div.dataset.id = `swiper_wrapper_${data.id}`;

      card_body_div.appendChild(swiper_wrapper_div);

      const swiper_next_div = document.createElement('div');
      swiper_next_div.classList.add('swiper-button-next');

      card_body_div.appendChild(swiper_next_div);

      const swiper_prev_div = document.createElement('div');
      swiper_prev_div.classList.add('swiper-button-prev');

      card_body_div.appendChild(swiper_prev_div);

      // 푸터
      const card_footer_div = document.createElement('div');
      card_footer_div.classList.add('card_footer');
      card_footer_div.classList.add('relative_box');

      const heart_container_div = document.createElement('div');
      heart_container_div.classList.add('heart_container');
      heart_container_div.classList.add('pointer');
      heart_container_div.classList.add('left');
      heart_container_div.classList.add('relative_box');
      heart_container_div.onclick = heart_event;

      let heart_img;
      heart_img = document.createElement('img');
      heart_img.classList.add('heart_img');
      heart_img.dataset.id = `heart_img_${id}`;
      if (is_empty(data.is_heart_click)) {
         heart_img.src = `/images/free-icon-heart-empty.png`;
      } else {
         heart_img.src = `/images/free-icon-hearts-red.png`;
      }

      heart_container_div.appendChild(heart_img);
      heart_container_div.dataset.id = id;

      const heart_cnt_div = document.createElement('div');
      heart_cnt_div.classList.add('clear');
      heart_cnt_div.classList.add('heart_cnt');
      heart_cnt_div.dataset.id = `heart_text_${id}`;
      heart_cnt_div.innerHTML = `좋아요 ${data.heart_cnt}개`;

      heart_container_div.appendChild(heart_cnt_div);

      card_footer_div.appendChild(heart_container_div);

      const chat_container = document.createElement('div');
      chat_container.classList.add('heart_container');
      chat_container.classList.add('pointer');
      chat_container.classList.add('left');
      chat_container.classList.add('margin_left_30');


      const chat_img = document.createElement('img');
      chat_img.src = `/images/free-icon-chat-bubble.png`;
      chat_img.classList.add('chat_img');
      chat_img.dataset.id = id;
      chat_img.onclick = chat_event;

      chat_container.appendChild(chat_img);

      card_footer_div.appendChild(chat_container);

      card_item.appendChild(card_footer_div);

      // 마지막
      home_container2.appendChild(card_item);

   }

   function follow_event() {
      const id = this.dataset.id.split('_')[1];
      const opponent_email = this.dataset.create_user;

      $.ajax({
         url: '/main_board/post_follow.php',
         type: 'POST',
         data: {
            opponent_email
         },
         dataType: 'json',
         success: function (data) {
            const result = data.result;
            if (result) {
               const is_followed = data.is_followed;
               const follow_element = document.querySelector(`[data-id="follow_${id}"]`);

               if (is_followed) {
                  follow_element.innerHTML = `팔로잉`;
               } else {
                  follow_element.innerHTML = `팔로우`;
               }
            }
         },
         error: function (xhr, status, error) {
            console.log(error);
         }
      });
   }

   function heart_event() {
      const main_board_id = this.dataset.id;

      $.ajax({
         url: '/main_board/post_heart.php',
         type: 'POST',
         data: {
            main_board_id
         },
         dataType: 'json',
         success: function (data) {
            const result = data.result;
            if (result) {
               const is_heart_exist = data.is_heart_exist;
               const total_cnt = data.total_cnt;
               const heart_cnt_div = document.querySelector(`[data-id=heart_text_${main_board_id}]`);
               const heart_img = document.querySelector(`[data-id=heart_img_${main_board_id}]`);
               heart_cnt_div.innerHTML = `좋아요 ${total_cnt}개`;
               if (is_heart_exist) {
                  heart_img.src = `/images/free-icon-hearts-red.png`;
               } else {
                  heart_img.src = `/images/free-icon-heart-empty.png`;
               }
            }
         },
         error: function (xhr, status, error) {
            console.log(error);
         }
      });
   }

   function chat_event() {
      const id = this.dataset.id;
      window.location.href = `/main_board/main_board_detail_read.php?main_board_id=${id}`;
   }

   // 파일 ui 렌더링
   function file_ui_add(file_data, id) {
      const swiper_wrapper = document.querySelector(`[data-id="swiper_wrapper_${id}"]`);

      const swiper_slide = document.createElement('div');
      swiper_slide.classList.add('swiper-slide');

      const img = document.createElement('img');
      img.src = file_data.path;

      swiper_slide.appendChild(img);

      swiper_wrapper.appendChild(swiper_slide);

   }

</script>


<script>
   $(document).on('ready', function () {
      read_notice();
   });

   function close_event() {
      const id = this.dataset.id;
      close_notice(id);
   }

   function close_one_day_event() {
      const id = this.dataset.id;
      close_notice(id);

      // 하루 #동안 보지 않는 쿠키 설정
      setCookie(`NO_NOTICE_${id}`, uuidv4(), 1);
   }

   function close_one_week_event() {
      const id = this.dataset.id;
      close_notice(id);

      // 일주일 동안 보지 않는 쿠키 설정
      setCookie(`NO_NOTICE_${id}`, uuidv4(), 7);
   }

   function close_notice(id) {
      // 해당 공지사항 닫기
      const notice_wrap = document.querySelector(`[data-id="notice_wrap_${id}"]`);
      notice_wrap.style.display = 'none';

      // 공지사항이 전부 닫혔는지 확인하고, 다 닫혔다면 어두운 영역을 없앤다.
      chk_all_back_close();

   }

   function chk_all_back_close() {
      // 모든 공지사항이 전부 닫혔는지 확인하여 다 닫혔다면 어두운 영역 풀기
      const notice_wraps = document.querySelectorAll('.notice_wrap');
      let is_all_close = true;
      for (const notice_item of notice_wraps) {
         const display = notice_item.style.display;
         if (display != 'none') {
            is_all_close = false;
            break;
         }
      }

      // 어두운 영역 닫기
      if (is_all_close) {
         document.querySelector('.notice_bg').style.display = 'none';
      }
   }

   function read_notice() {
      let url = `/free_board/get_free_board_notice.php`;

      $.ajax({
         url: url,
         type: 'GET',
         dataType: 'json',
         success: function (datas) {

            for (const data of datas) {
               const {
                  id,
                  title,
                  content,
                  views,
                  create_user,
                  type,
                  create_nick_name,
                  create_date
               } = data;

               if (getCookie(`NO_NOTICE_${id}`)) {
                  continue;
               }

               const notice_container = document.querySelector('.notice_container');

               const notice_wrap = document.createElement('div');
               notice_wrap.classList.add('notice_wrap');
               notice_wrap.dataset.id = `notice_wrap_${id}`;

               // 헤더 영역
               const notice_header = document.createElement('div');
               notice_header.classList.add('notice_header');
               notice_header.classList.add('relative_box');

               const notice_title = document.createElement('div');
               notice_title.classList.add('notice_title');
               notice_title.classList.add('left');
               notice_title.classList.add('relative_box');
               notice_title.classList.add('relative_viertical');
               notice_title.innerHTML = title;

               notice_header.appendChild(notice_title);

               const notice_close_img = document.createElement('img');
               notice_close_img.classList.add('notice_close_img');
               notice_close_img.classList.add('pointer');
               notice_close_img.classList.add('right');
               notice_close_img.classList.add('relative_box');
               notice_close_img.classList.add('relative_viertical');
               notice_close_img.src = '/images/close_img.png';
               notice_close_img.onclick = close_event;
               notice_close_img.dataset.id = id;

               notice_header.appendChild(notice_close_img);

               notice_wrap.appendChild(notice_header);

               // 컨텐츠 영역
               const notice_content = document.createElement('div');
               notice_content.classList.add('notice_content');
               notice_content.innerHTML = content;

               notice_wrap.appendChild(notice_content);

               // 푸터 영역
               const notice_footer = document.createElement('div');
               notice_footer.classList.add('notice_footer');
               notice_footer.classList.add('relative_box');

               const close_one_day_btn = document.createElement('div');
               close_one_day_btn.classList.add('black');
               close_one_day_btn.classList.add('margin_left');
               close_one_day_btn.classList.add('pointer');
               close_one_day_btn.classList.add('left');
               close_one_day_btn.classList.add('relative_box');
               close_one_day_btn.classList.add('relative_viertical');
               close_one_day_btn.innerHTML = '하루 동안 계속 닫기';
               close_one_day_btn.dataset.id = id;
               close_one_day_btn.onclick = close_one_day_event;

               const close_one_week_btn = document.createElement('div');
               close_one_week_btn.classList.add('black');
               close_one_week_btn.classList.add('margin_left');
               close_one_week_btn.classList.add('pointer');
               close_one_week_btn.classList.add('left');
               close_one_week_btn.classList.add('relative_box');
               close_one_week_btn.classList.add('relative_viertical');
               close_one_week_btn.innerHTML = '일주일 동안 계속 닫기';
               close_one_week_btn.dataset.id = id;
               close_one_week_btn.onclick = close_one_week_event;

               notice_footer.appendChild(close_one_day_btn);
               notice_footer.appendChild(close_one_week_btn);

               notice_wrap.appendChild(notice_footer);

               notice_container.prepend(notice_wrap);

            }

            chk_all_back_close();

         },
         error: function (xhr, status, error) {
            console.log(error);
         }
      });
   }
</script>


<body>
   <?php require './header.php'; ?>

   <!-- modal 영역 -->
   <div class="notice_bg"></div>
   <div class="notice_container">
      <!-- <div class="notice_wrap">
        <div class="notice_header relative_box">
            <div class="notice_title left relative_box relative_viertical">타이틀</div>
            <img id="close_img" src="/images/close_img.png"
                class="pointer notice_close_img right relative_box relative_viertical"></img>
        </div>
        <div class="notice_content">
            <div>타이틀</div>
            <div>닫기 이미지</div>
        </div>
        <div class="notice_footer relative_box ">
            <div class="black margin_left pointer left relative_box relative_viertical">하루 동안 계속 닫기</div>
            <div class="black margin_left_30 pointer left relative_box relative_viertical">일주일 동안 계속 닫기</div>
        </div>
    </div> -->
   </div>

   <div  class="home_container">
      <div class="home_container2" id="react_app">
      </div>
   </div>

   <!-- <div id="test" style="float:left;"></div> -->

   <script src="dist/react_bundle.js"></script>

   <!-- banner section start -->
   <?php require './footer.php'; ?>

   <?php require './copyright.php'; ?>
</body>

</html>