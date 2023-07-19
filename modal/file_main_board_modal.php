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
            file_add(files, dropzoneElement, 'new');
            console.log('drop');
        });

        // 파일 선택 버튼 이벤트 처리
        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            file_add(files, dropzoneElement, 'new');
        });

        // 모달이 닫혔을때
        $('#img_upload_modal').on('hidden.bs.modal', function (e) {
            // 클리어
            clear(dropzoneElement);
        });

        // 모달이 열렸을 때
        $('#img_upload_modal').on('show.bs.modal', function (e) {
            modal_already_file_list = Object.assign([], already_file_list);
            modal_new_file_list = Object.assign([], new_file_list);
            modal_remove_file_list = Object.assign([], remove_file_list);

            /**
             * 파라미터 1: 파일리스트 데이터
             * 파라미터 2: 파일추가될 element
             * 파라미터 3: 'new' or 'already' 값으로 
             *  이미 저장되어져 있는 파일인지, 새롭게 추가될 파일인지 구분
             * 파라미터 4: UI만 업데이트할지 데이터까지 업데이트할지 선택
             *  - true : UI만 업데이트
             *  - false : UI와 함께 데이터도 수정
             * 파라미터 5: origin을 기준으로 작업할지
             *  modal을 기준으로 작업할지 선택
             *  - true : origin을 기준으로 작업
             *  - false : modal을 기준으로 작업
             */
            file_add(modal_new_file_list, dropzoneElement, 'new', true);
            file_add(modal_already_file_list, dropzoneElement, 'already', true);
        });

        // 확인 버튼 눌렀을 때
        $('#apply_btn').on('click', function (e) {
            const main_grid_container = document.querySelector('#main_grid_container');

            /**
             * modal 변수들을 origin 변수들로 옮긴다
             */
            already_file_list = Object.assign([], modal_already_file_list);
            new_file_list = Object.assign([], modal_new_file_list);
            remove_file_list = Object.assign([], modal_remove_file_list);

            // UI만 클리어
            clear(main_grid_container, true);

            /**
             * 파라미터 1: 파일리스트 데이터
             * 파라미터 2: 파일추가될 element
             * 파라미터 3: 'new' or 'already' 값으로 
             *  이미 저장되어져 있는 파일인지, 새롭게 추가될 파일인지 구분
             * 파라미터 4: UI만 업데이트할지 데이터까지 업데이트할지 선택
             *  - true : UI만 업데이트
             *  - false : UI와 함께 데이터도 수정
             * 파라미터 5: origin을 기준으로 작업할지
             *  modal을 기준으로 작업할지 선택
             *  - true : origin을 기준으로 작업
             *  - false : modal을 기준으로 작업
             */
            file_add(new_file_list, main_grid_container, 'new', true, true);
            file_add(already_file_list, main_grid_container, 'already', true, true);

            $('#img_upload_modal').modal('hide');
        });


    });

    /**
     * 파일 추가
     * files : 추가할 파일들
     * parent : 추가한 파일을 표시할 요소
     * type : already면 이미 저장되어져 있는 이미지
     *          new면 이번에 새롭게 추가하는 이미지
     * only_ui : true면 UI렌더링만 관여
     *           false면 데이터를 추가삭제 관여
     * 
     * is_origin : 누구에 관해서 작업을 진행할지 선택
     *  - true면 부모, false면 모달
     *  - origin : 부모
     *  - modal : 모달
     *
     */
    function file_add(files, parent, type, only_ui, is_origin) {

        if (files.length <= 0) return;

        for (const file of files) {
            const uuid = uuidv4();
            file.uuid = uuid;

            /**
             * 새롭게 추가되는 이미지이고, 데이터 변경이 가능하면
             * modal_new_file_list 에 새롭게 추가되는 이미지 file 추가
             */
            if (type === 'new' && !only_ui) {
                modal_new_file_list.push(file);
            }

            /**
             * UI 동적 렌더링
             */
            const fileContainer = document.createElement('div');
            fileContainer.classList.add('main_grid_file');
            fileContainer.dataset.uuid = uuid;

            const closeImg = document.createElement('img');
            closeImg.src = '/images/close.png';
            closeImg.classList.add('close-img');
            closeImg.dataset.uuid = uuid;
            closeImg.dataset.is_origin = is_origin;

            /**
             * 닫는 버튼 이미지에 현재 이미지가 새롭게 추가된건지
             * 기존에 존재하던 이미지인지 구분하기 위해 dataset을 추가시킨다.
             */
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

            /**
             * 파일명이 12자리 이상일 경우, 중간에 ... 을 추가해서 15자리 자리수로 맞춘다.
             */
            let file_name = truncateString(file.name, 12);

            fileText.textContent = file_name;
            fileText.classList.add('file_txt');

            fileContainer.appendChild(fileImg);
            fileContainer.appendChild(fileText);

            /**
             * 이미 존재하는 이미지의 경우 src에 실제 파일 경로를 넣어서 보여준다.
             * 새롭게 추가되는 이미지의 경우 file data를 읽어서 src에 추가시켜 보여준다.
             * 모달에서 실행한 경우, 마지막에 컨테이너에 추가
             */
            if (type === 'already') {
                fileImg.src = file.path;
                parent.appendChild(fileContainer);
            } else if (type === 'new') {
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

    function clear(dropzoneElement, only_ui) {
        if (!only_ui) {
            modal_already_file_list = [];
            modal_new_file_list = [];
            modal_remove_file_list = [];
        }

        while (dropzoneElement.firstChild) {
            dropzoneElement.removeChild(dropzoneElement.firstChild);
        }
    }

    function close_img() {
        const uuid = this.dataset.uuid;
        const type = this.dataset.type;
        const is_origin = this.dataset.is_origin;

        if (type === 'already') {
            let removable_element;

            if (is_empty(is_origin)) {
                // already file list 에서 해당 요소 제거
                for (let i = 0; i < modal_already_file_list.length; i++) {
                    if (modal_already_file_list[i].uuid === uuid) {
                        removable_element = modal_already_file_list[i];
                        modal_already_file_list.splice(i, 1);
                        break;
                    }
                }
                // remove file list 에 해당 요소 추가
                modal_remove_file_list.push(removable_element);
            } else {
                // modal already file list 에서 해당 요소 제거
                for (let i = 0; i < already_file_list.length; i++) {
                    if (already_file_list[i].uuid === uuid) {
                        removable_element = already_file_list[i];
                        already_file_list.splice(i, 1);
                        break;
                    }
                }
                // remove file list 에 해당 요소 추가
                remove_file_list.push(removable_element);
            }


        } else if (type === 'new') {
            if (is_empty(is_origin)) {
                // new file list 에서 해당 요소 제거
                for (let i = 0; i < modal_new_file_list.length; i++) {
                    if (modal_new_file_list[i].uuid === uuid) {
                        modal_new_file_list.splice(i, 1);
                        break;
                    }
                }
            } else {
                // modal new file list 에서 해당 요소 제거
                for (let i = 0; i < new_file_list.length; i++) {
                    if (new_file_list[i].uuid === uuid) {
                        new_file_list.splice(i, 1);
                        break;
                    }
                }
            }
        }

        // UI 삭제
        const remove_element = document.querySelector(`.main_grid_file[data-uuid='${uuid}']`);
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

</script>

<div class="drop-zone__files_local flex_container" style="min-height: 60px; max-height: 140px; overflow: auto;"></div>

<!-- Modal -->
<div class="modal fade" id="img_upload_modal" tabindex="-1" role="dialog" aria-labelledby="img_upload_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- 모달 이름 변경 -->
                <h5 class="modal-title" id="img_upload_modalLabel">게시글 이미지 업로드</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div style="border-bottom: 1px solid #e9ecef; padding: 10px;">
                <!-- 파일 선택 -->
                <input type="file" id="file-input" multiple />
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