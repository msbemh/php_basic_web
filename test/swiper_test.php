<!DOCTYPE html>
<html lang="en">

<head>
  <?php require './head.php'; ?>
  <!-- Link Swiper's CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

  <!-- Demo styles -->
  <style>
    html,
    body {
      position: relative;
      height: 100%;
    }

    body {
      background: #eee;
      font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
      font-size: 14px;
      color: #000;
      margin: 0;
      padding: 0;
    }

    .swiper {
      width: 100%;
      height: 100%;
    }

    .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .swiper-slide img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <?php require './header.php'; ?>

  <div class="free_board_section layout_padding create_free_board">
        <div class="container">
            <div class="main_view_flex_container">
                <div class="main_view_img_box relative_box mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">Slide 1</div>
                        <div class="swiper-slide">Slide 2</div>
                        <div class="swiper-slide">Slide 3</div>
                        <div class="swiper-slide">Slide 4</div>
                        <div class="swiper-slide">Slide 5</div>
                        <div class="swiper-slide">Slide 6</div>
                        <div class="swiper-slide">Slide 7</div>
                        <div class="swiper-slide">Slide 8</div>
                        <div class="swiper-slide">Slide 9</div>
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
        </div>
    </div>


  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper(".mySwiper", {
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
  </script>


  <?php require './footer.php'; ?>

  <?php require './copyright.php'; ?>

</body>

</html>