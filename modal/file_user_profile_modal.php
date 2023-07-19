<script>
    var modal_already_file_list = [];
    var modal_new_file_list = [];
    var modal_remove_file_list = [];
    var dropzoneElement;
    $(document).on('ready', function () {
        const dropZone = document.querySelector('#drop_file_zone');
        // 파일 목록을 표시할 요소를 가져옵니다.
        dropzoneElement = document.querySelector('.drop-zone__files');

        // 파일 드래그 앤 드롭 이벤트 처리
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
            console.log('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
            console.log('dragleave');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            file_ui_add(files, dropzoneElement, 'new');
            console.log('drop');
        });

        // 파일 선택 버튼 이벤트 처리
        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            file_ui_add(files, dropzoneElement, 'new');
        });

        function clear(only_ui) {
            if (!only_ui) {
                modal_already_file_list = [];
                modal_new_file_list = [];
                modal_remove_file_list = [];
            }

            while (dropzoneElement.firstChild) {
                dropzoneElement.removeChild(dropzoneElement.firstChild);
            }
        }

        // 파일 업로드를 처리하는 함수
        function file_ui_add(files, parent, type, only_ui, is_origin) {
            // 유저 프로필은 무조건 file 1개만 보여야 하기에, UI 추가전에 클리어시켜준다
            // true : UI만 clear, 데이터는 claer x
            clear(true);

            if (files.length <= 0) return;

            const file = files[0];
            const uuid = uuidv4();
            file.uuid = uuid;

            if (type === 'new' && !only_ui) {
                // 유저 프로필은 무조건 1개만 가져야하기 때문에 초기화후 추가
                modal_new_file_list = [];
                modal_new_file_list.push(file);
                const removable_element = modal_already_file_list[0];
                modal_already_file_list = [];
                if(removable_element) modal_remove_file_list.push(removable_element);
            }


            const fileContainer = document.createElement('div');
            fileContainer.classList.add('file');
            fileContainer.dataset.uuid = uuid;

            const closeImg = document.createElement('img');
            closeImg.src = '/images/close.png';
            closeImg.classList.add('close-img');
            closeImg.dataset.uuid = uuid;

            if (type === 'already') {
                closeImg.dataset.type = 'already';
            } else if (type === 'new') {
                closeImg.dataset.type = 'new';
            }

            closeImg.onclick = close_img;
            fileContainer.appendChild(closeImg);

            const fileImg = document.createElement('img');
            fileImg.classList.add('file-img');
            const fileText = document.createElement('div');


            let file_name = truncateString(file.name, 12);

            fileText.textContent = file_name;
            fileText.classList.add('file_txt');

            fileContainer.appendChild(fileImg);
            fileContainer.appendChild(fileText);

            if (type === 'already') {
                fileImg.src = file.path;
                parent.appendChild(fileContainer);
            } else if (type === 'new') {
                if (is_origin) {
                    readURL(file, parent);
                } else {
                    readURL(file, fileImg)
                        .then(function () {
                            parent.appendChild(fileContainer);
                        }, function (error) {
                            console.log(error); // Stacktrace
                        });
                }

            }


            // 파일을 서버로 전송하는 코드를 추가합니다.
            // 예를 들어, AJAX 요청을 사용하여 파일을 업로드할 수 있습니다.
            document.querySelector('#file-input').value = ""
        }

        function close_img() {
            const uuid = this.dataset.uuid;
            const type = this.dataset.type;

            if (type === 'already') {
                let removable_element;

                // modal already file list 에서 해당 요소 제거
                for (let i = 0; i < modal_already_file_list.length; i++) {
                    if (modal_already_file_list[i].uuid === uuid) {
                        removable_element = modal_already_file_list[i];
                        modal_already_file_list.splice(i, 1);
                        break;
                    }
                }

                // modal remove file list 에 해당 요소 추가
                modal_remove_file_list.push(removable_element);
            } else if (type === 'new') {
                // modal new file list 에서 해당 요소 제거
                for (let i = 0; i < modal_new_file_list.length; i++) {
                    if (modal_new_file_list[i].uuid === uuid) {
                        modal_new_file_list.splice(i, 1);
                        break;
                    }
                }
            }

            // UI 삭제
            const remove_element = document.querySelector(`.file[data-uuid='${uuid}']`);
            remove_element.remove();
        }

        function readURL(file, imgItem) {
            return new Promise(function (resolve, reject) {
                try {
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            try {
                                imgItem.src = e.target.result;
                                resolve();
                            } catch (e) {
                                reject(e);
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imgItem.src = "";
                    }
                } catch (e) {
                    reject(e);
                }

            });
        }

        function truncateString(str, maxLength) {
            if (str.length > maxLength) {
                return str.substring(0, 6) + '...' + str.slice(-6);
            }
            return str;
        }

        function uuidv4() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }

        // 모달이 닫혔을때
        $('#img_upload_modal').on('hidden.bs.modal', function (e) {
            // 클리어
            clear();
        });

        // 모달이 열렸을 때
        $('#img_upload_modal').on('show.bs.modal', function (e) {
            modal_already_file_list = Object.assign([], already_file_list);
            modal_new_file_list = Object.assign([], new_file_list);
            modal_remove_file_list = Object.assign([], remove_file_list);

            // true 1: 오직 UI만 렌더링, 데이터 변경 x
            file_ui_add(modal_new_file_list, dropzoneElement, 'new', true);
            file_ui_add(modal_already_file_list, dropzoneElement, 'already', true);
        });

        // 확인 버튼 눌렀을 때
        $('#apply_btn').on('click', function (e) {
            const user_profile_img_element = document.querySelector('#user_profile_img');

            already_file_list = Object.assign([], modal_already_file_list);
            new_file_list = Object.assign([], modal_new_file_list);
            remove_file_list = Object.assign([], modal_remove_file_list);

            // true 1: 오직 UI만 렌더링, 데이터 변경 x
            // true 2: origin인지 modal인지 판별, origin이면 true
            file_ui_add(new_file_list, user_profile_img_element, 'new', true, true);
            file_ui_add(already_file_list, user_profile_img_element, 'already', true, true);

            if (already_file_list.length == 0 && new_file_list.length == 0) {
                user_profile_img_element.src = "https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77";
            }

            $('#img_upload_modal').modal('hide');
            // if (new_file_list.length > 0) {
            //     const file = new_file_list[0];
            //     readURL(file, user_profile_img_element)
            //         .then(function () {
            //             file_data_local_list = new_file_list;
            //             $('#img_upload_modal').modal('hide');
            //         }, function (error) {
            //             console.log(error); // Stacktrace
            //         });
            // } else {
            //     file_data_local_list = new_file_list;
            //     user_profile_img_element.src = "https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_profile_77.png?type=c77_77";
            //     $('#img_upload_modal').modal('hide');
            // }

            // const local_file_list_element = document.querySelector('.drop-zone__files_local');
            // handleLocalFileAdd(new_file_list, local_file_list_element);
        });


    });

</script>

<div class="drop-zone__files_local flex_container" style="min-height: 60px; max-height: 140px; overflow: auto;"></div>

<!-- Modal -->
<div class="modal fade" id="img_upload_modal" tabindex="-1" role="dialog" aria-labelledby="img_upload_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- 모달 이름 변경 -->
                <h5 class="modal-title" id="img_upload_modalLabel">프로필 이미지 업로드</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div style="border-bottom: 1px solid #e9ecef; padding: 10px;">
                <!-- 파일 선택 -->
                <input type="file" id="file-input" />
            </div>
            <div style="border-bottom: 1px solid #e9ecef;">
                <!-- 추가된 파일 리스트 표현 영역 -->
                <div class="drop-zone__files grid_container"
                    style="min-height: 60px; max-height: 140px; overflow: auto;"></div>
            </div>
            <div class="modal-body" style="height:150px;">
                <!-- drop-zone 영역 -->
                <div id="drop_file_zone">
                    <div id="drag_upload_file">
                        <p>Drop file(s) here</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">취소</button>
                <button id="apply_btn" type="button" class="btn btn-dark">확인</button>
            </div>
        </div>
    </div>
</div>