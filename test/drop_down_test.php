<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>

        $(document).on('ready', function () {

            $(".free_board_section .dropdown-item").on('click', function () {
                window.current_category_name = this.dataset.category;
                const dropdown_menu_button = document.querySelector('#dropdownMenuButton');
                const text = this.innerHTML;

                // 카테고리 txt 수정
                if (window.current_category_name === 'title') {
                    dropdown_menu_button.innerHTML = text;
                } else if (window.current_category_name === 'create_user') {
                    dropdown_menu_button.innerHTML = text;
                }
            });


        });

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div class="dropdown relative_box" style="display: block; width: 100px; height: 100px; float: left;">
        <div style="display: block; width: 100px; height: 100px;" 
            class="add_info_container" 
            data-toggle="dropdown" >
        </div>
        <div class="dropdown-menu" >
            <a class="dropdown-item" >수정</a>
            <a class="dropdown-item" >삭제</a>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>