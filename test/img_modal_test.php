<?php require './head.php'; ?>

<script>
    let file_data_temp_list = [];
    let file_data_local_list = [];
    $(document).on('ready', function () {
        const dropZone = document.querySelector('#drop_file_zone');
        // 파일 목록을 표시할 요소를 가져옵니다.
        const dropzoneElement = document.querySelector('.drop-zone__files');

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
            handleFileAdd(files, dropzoneElement);
            console.log('drop');
        });

        // 파일 선택 버튼 이벤트 처리
        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            handleFileAdd(files, dropzoneElement);
        });

        // 파일 업로드를 처리하는 함수
        function handleFileAdd(files, parent) {
            for (const file of files) {
                const uuid = uuidv4();
                file.uuid = uuid;
                file_data_temp_list.push(file);

                const fileContainer = document.createElement('div');
                fileContainer.classList.add('file');
                fileContainer.dataset.uuid = uuid;

                const closeImg = document.createElement('img');
                closeImg.src = './images/close.png';
                closeImg.classList.add('close-img');
                closeImg.dataset.uuid = uuid;
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

                readURL(file, fileImg)
                    .then(function () {
                        parent.appendChild(fileContainer);
                    }, function (error) {
                        console.log(error); // Stacktrace
                    });


                // 파일을 서버로 전송하는 코드를 추가합니다.
                // 예를 들어, AJAX 요청을 사용하여 파일을 업로드할 수 있습니다.
            }
            document.querySelector('#file-input').value = ""
        }

        // 파일 업로드를 처리하는 함수
        function handleLocalFileAdd(files, parent) {
            for (const file of files) {
                const uuid = uuidv4();
                file.uuid = uuid;
                file_data_local_list.push(file);

                const fileContainer = document.createElement('div');
                fileContainer.classList.add('file');
                fileContainer.dataset.uuid = uuid;

                const closeImg = document.createElement('img');
                closeImg.src = './images/close.png';
                closeImg.classList.add('close-img');
                closeImg.dataset.uuid = uuid;
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

                readURL(file, fileImg)
                    .then(function () {
                        parent.appendChild(fileContainer);
                    }, function (error) {
                        console.log(error); // Stacktrace
                    });


                // 파일을 서버로 전송하는 코드를 추가합니다.
                // 예를 들어, AJAX 요청을 사용하여 파일을 업로드할 수 있습니다.
            }
            document.querySelector('#file-input').value = ""
        }

        function close_img() {
            const uuid = this.dataset.uuid;

            for (let i = 0; i < file_data_temp_list.length; i++) {
                if (file_data_temp_list[i].uuid === uuid) {
                    file_data_temp_list.splice(i, 1);
                    break;
                }
            }

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

        $('#img_upload_modal').on('hidden.bs.modal', function (e) {
            while (dropzoneElement.firstChild) {
                dropzoneElement.removeChild(dropzoneElement.firstChild);
            }
        });

        $('#apply_btn').on('click', function (e) {
            const local_file_list_element = document.querySelector('.drop-zone__files_local');
            handleLocalFileAdd(file_data_temp_list, local_file_list_element);
        });


    });

    function send() {
        const title = document.querySelector('#title').value;
        // console.log('title:', title);

        const formData = new FormData();
        formData.append('title', title);

        // 파일 목록을 FormData에 추가합니다.
        for (let i = 0; i < file_data_local_list.length; i++) {
            const file = file_data_local_list[i];
            formData.append(`file_${i}`, file);
        }

        for (let key of formData.keys()) {
            console.log(key, ":", formData.get(key));
        }

        $.ajax({
            url: `send_test_file.php`,
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                window.is_valid_id = data.is_valid;
                if (window.is_valid_id) {
                    document.querySelector('#email_verify_txt').style.color = 'green';
                    document.querySelector('#email_verify_txt').innerHTML = '유효한 이메일 입니다.';
                } else {
                    document.querySelector('#email_verify_txt').style.color = 'red';
                    document.querySelector('#email_verify_txt').innerHTML = '중복된 이메일 입니다.';
                }

            },
            error: function (xhr, status, error) {
                window.is_valid_id = false;
                document.querySelector('#email_verify_txt').style.color = 'red';
                document.querySelector('#email_verify_txt').innerHTML = '서버 에러';
                console.log(error);
            },
            complete: function () {
                document.querySelector('#email_verify_txt').style.display = 'block';
            }
        });
    }


</script>

<style>
    
</style>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#img_upload_modal">
    Launch demo modal
</button>

<div class="drop-zone__files_local flex_container" style="min-height: 60px; max-height: 140px; overflow: auto;"></div>

<script src="custom.js"></script>

<input id="title" value="title" />
<button type="button" onclick="send()">서버로 보내기</button>

<!-- Modal -->
<div class="modal fade" id="img_upload_modal" tabindex="-1" role="dialog" aria-labelledby="img_upload_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="img_upload_modalLabel">프로필 이미지 업로드</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div style="border-bottom: 1px solid #e9ecef; padding: 10px;">
                <input type="file" id="file-input" multiple />
            </div>
            <div style="border-bottom: 1px solid #e9ecef;">
                <div class="drop-zone__files grid_container"
                    style="min-height: 60px; max-height: 140px; overflow: auto;"></div>
            </div>
            <div class="modal-body" style="height:150px;">
                <div id="drop_file_zone">
                    <div id="drag_upload_file">
                        <p>Drop file(s) here</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                <button id="apply_btn" type="button" class="btn btn-primary">확인</button>
            </div>
        </div>
    </div>
</div>