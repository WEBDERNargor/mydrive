@extends('user.layout')
@section('head')
    <title>Upload | {{ NAME() }}</title>
@endsection
@section('content')
    <div class="mt-20 max-w-2xl mx-auto ">
        <form id="uploadForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">

            <div class="w-full h-64 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center relative"
                id="dropzone">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Drag and drop files here or</p>
                    <button type="button" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        id="browseButton">
                        Browse Files
                    </button>
                    <input type="file" multiple class="hidden" id="fileInput" name="files[]"
                        accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.mp4,.mov,.wmv,.flv,.zip,.rar">
                </div>
            </div>

            <div id="fileList" class="space-y-2"></div>
            <div id="errorMessages" class="text-red-500 mt-2"></div>
            <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Upload Files
            </button>
        </form>

    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var token = getCookie('login_token');
            const dropzone = $('#dropzone');
            const fileInput = $('#fileInput');
            const fileList = $('#fileList');
            const errorMessages = $('#errorMessages');
            const chunkSize = 2 * 1024 * 1024; // 2MB per chunk
            let isOnline = navigator.onLine;
            let uploadInProgress = false;

            const allowedTypes = [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
                'image/gif', // MIME type สำหรับ gif
                'image/png', // MIME type สำหรับ png
                'image/jpeg', // MIME type สำหรับ jpeg
                'video/mp4', // MIME type สำหรับ mp4
                'video/quicktime', // MIME type สำหรับ mov
                'video/x-ms-wmv', // MIME type สำหรับ wmv
                'video/x-flv' // MIME type สำหรับ flv
            ];

            const maxFileSize = 20480 * 1024 * 1024; // 2GB
            let selectedFiles = []; // Array to store selected files
            let filesUploaded = 0; // Counter for uploaded files

            // ตรวจสอบการเชื่อมต่ออินเทอร์เน็ต
            window.addEventListener('online', function() {
                isOnline = true;
                if (uploadInProgress) {
                    // ถามผู้ใช้ว่าต้องการอัพโหลดต่อหรือไม่
                    if (confirm('การเชื่อมต่ออินเทอร์เน็ตกลับมาแล้ว ต้องการอัพโหลดไฟล์ใหม่หรือไม่?')) {
                        // ลบไฟล์เก่าและเริ่มอัพโหลดใหม่
                        cleanupAndRestartUpload();
                    } else {
                        // ยกเลิกการอัพโหลด
                        cleanupUpload();
                        resetUpload();
                    }
                }
            });

            window.addEventListener('offline', function() {
                isOnline = false;
                if (uploadInProgress) {
                    alert('ขาดการเชื่อมต่ออินเทอร์เน็ต กรุณารอสักครู่...');
                    // ทำความสะอาดไฟล์ที่อัพโหลดค้างไว้
                    cleanupUpload();
                }
            });

            // ฟังก์ชันสำหรับลบไฟล์ .part
            async function cleanupUpload() {
                try {
                    const formData = new FormData();
                    formData.append('fileName', currentFile.name);

                    const response = await fetch('/api/cleanup-upload', {
                        method: 'POST',
                        headers: {
                            'authorization': 'Bearer ' + token
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.status === 'error') {
                        console.error('Failed to cleanup upload:', result.message);
                    }
                } catch (error) {
                    console.error('Error cleaning up upload:', error);
                }
            }

            // รีเซ็ตสถานะการอัพโหลด
            function resetUpload() {
                uploadInProgress = false;
                currentChunk = 0;
                currentFile = null;
                // รีเซ็ต progress bar
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = '0%';
                    progressBar.textContent = '0%';
                }
            }

            // ลบไฟล์เก่าและเริ่มอัพโหลดใหม่
            async function cleanupAndRestartUpload() {
                await cleanupUpload();
                resetUpload();
                // เริ่มอัพโหลดใหม่
                if (currentFile) {
                    uploadFile(currentFile);
                }
            }

            $('#browseButton').click(() => fileInput.click());

            dropzone.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-blue-500');
            }).on('dragleave', function() {
                $(this).removeClass('border-blue-500');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-500');
                handleFiles(e.originalEvent.dataTransfer.files);
            });

            fileInput.on('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                errorMessages.empty();
                Array.from(files).forEach((file, index) => {
                    if (!validateFile(file)) return;
                    if (selectedFiles.some(f => f.name === file.name)) {
                        errorMessages.append(`<p>${file.name}: File already selected</p>`);
                        return;
                    }
                    selectedFiles.push(file); // Add valid file to array

                    // แสดงขนาดไฟล์ในรูปแบบที่อ่านง่าย
                    const fileSize = formatFileSize(file.size);
                    const preview = $(`
                    <div data-filename='${file.name}' class="flex items-center justify-between p-2 bg-gray-100 rounded">
                        <div class="flex items-center flex-grow">
                            <i class="fas fa-file mr-2"></i>
                            <div class="flex flex-col">
                                <span class="text-sm">${file.name}</span>
                                <span class="text-xs text-gray-500">${fileSize}</span>
                            </div>
                        </div>
                        <button type="button" class="cancel-btn text-red-500 hover:text-red-700 ml-2">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="progress-container w-full h-2 bg-gray-200 rounded-full mt-2 hidden">
                            <div style="width: 0%;" class="progress-bar h-2 bg-green-500 rounded-full" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                        </div>
                    </div>
                    `);

                    preview.find('button').click(function() {
                        let filename = preview.data('filename');
                        selectedFiles = selectedFiles.filter(f => f.name !== filename);
                        preview.remove();

                        const dt = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dt.items.add(file);
                        });
                        fileInput[0].files = dt.files;
                    });
                    fileList.append(preview);

                });
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function validateFile(file) {
                if (!allowedTypes.includes(file.type)) {
                    errorMessages.append(`<p>${file.name}: Invalid file type</p>`);
                    return false;
                }
                if (file.size > maxFileSize) {
                    errorMessages.append(`<p>${file.name}: File size exceeds 2GB</p>`);
                    return false;
                }
                return true;
            }

            $("#uploadForm").submit(function(e) {
                e.preventDefault();
                if (!isOnline) {
                    Swal.fire({
                        title: 'No Internet Connection',
                        text: 'Please check your internet connection and try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 5000,
                        timerProgressBar: true
                    });
                    return;
                }

                if (selectedFiles.length === 0) {
                    errorMessages.append('<p>Please select files to upload</p>');
                    return;
                }

                uploadInProgress = true;
                $('.cancel-btn').addClass('hidden');
                $('.progress-container').removeClass('hidden');

                // ซ่อนปุ่มยกเลิกและแสดง progress bar
                selectedFiles.forEach((file, index) => {
                    uploadFileInChunks(file, index);
                });
            });

            function uploadFileInChunks(file, index) {
                if (!isOnline) {
                    return;
                }
                let start = 0;
                let end = chunkSize;
                let chunkIndex = 0;

                function uploadNextChunk() {

                    const chunk = file.slice(start, end);
                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('fileName', file.name);
                    formData.append('chunkIndex', chunkIndex);
                    formData.append('totalChunks', Math.ceil(file.size / chunkSize));

                    $.ajax({
                        url: '/api/upload-chunk',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        headers: {
                            'authorization': 'Bearer ' + token
                        },
                        xhr: function() {
                            let xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    let percentComplete = (start + e.loaded) / file.size * 100;
                                    $(`#fileList > div[data-filename='${file.name}'] .progress-bar`)
                                        .css(
                                            'width', percentComplete + '%').attr(
                                            'aria-valuenow', percentComplete);
                                }
                            }, false);

                            // เพิ่ม event listener สำหรับการยกเลิก
                            xhr.addEventListener('abort', function() {
                                cleanupFailedUpload(file.name);
                            });

                            return xhr;
                        },
                        success: function(response) {
                            try {
                                if (typeof response !== 'object') {
                                    response = JSON.parse(response);
                                }
                            } catch (e) {
                                Swal.fire({
                                    title: 'Upload failed',
                                    text: 'Invalid server response',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    timer: 5000,
                                    timerProgressBar: true
                                });
                                return;
                            }

                            if (response.status === 'error') {
                                Swal.fire({
                                    title: 'Upload failed',
                                    text: response.message,
                                    icon: 'error',
                                    timer: 5000,
                                    timerProgressBar: true,
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }

                            if (end < file.size) {
                                start = end;
                                end = start + chunkSize;
                                chunkIndex++;
                                uploadNextChunk();
                            } else {
                                filesUploaded++;
                                if (filesUploaded === selectedFiles.length) {
                                    Swal.fire({
                                        title: 'Upload Complete',
                                        text: 'All files have been uploaded successfully!',
                                        icon: 'success',
                                        confirmButtonText: 'OK',
                                        timer: 5000,
                                        timerProgressBar: true,
                                    }).then((e) => {
                                        window.location.reload();
                                    });
                                }
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Upload failed:', textStatus, errorThrown);
                            if (!isOnline) {
                                errorMessages.html(
                                    '<p class="text-red-500">Internet connection lost. Upload paused.</p>'
                                );
                                return;
                            }
                            // เรียกใช้ฟังก์ชันทำความสะอาดเมื่อเกิดข้อผิดพลาด
                            cleanupFailedUpload(file.name);
                            Swal.fire({
                                title: 'Upload failed',
                                text: `An error occurred during the upload process: ${textStatus} - ${errorThrown}`,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                timer: 5000,
                                timerProgressBar: true
                            });
                        }
                    });
                }

                uploadNextChunk();
            }

            // เพิ่มฟังก์ชันสำหรับทำความสะอาดไฟล์ที่อัพโหลดไม่สำเร็จ
            function cleanupFailedUpload(fileName) {
                $.ajax({
                    url: '/api/cleanup-upload',
                    type: 'POST',
                    data: {
                        fileName: fileName
                    },
                    headers: {
                        'authorization': 'Bearer ' + token
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Error",
                            text: jqXHR.responseJSON.message,
                            icon: "error",
                            timer: 5000,
                            timerProgressBar: true,
                        });
                    }
                });
            }

            // เพิ่ม event listener สำหรับการปิดหน้าเว็บขณะกำลังอัพโหลด
            window.addEventListener('beforeunload', function(e) {
                if (filesUploaded < selectedFiles.length) {
                    selectedFiles.forEach(file => {
                        cleanupFailedUpload(file.name);
                    });
                }
            });

            async function uploadFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    currentFile = files[i];
                    await uploadFile(currentFile);
                }
            }

            async function uploadFile(file) {
                // รีเซ็ตสถานะการอัพโหลด
                resetUpload();

                const chunkSize = 1024 * 1024; // 1MB
                const totalChunks = Math.ceil(file.size / chunkSize);
                let currentChunk = 0;

                while (currentChunk < totalChunks) {
                    const start = currentChunk * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('fileName', file.name);
                    formData.append('chunkNumber', currentChunk + 1);
                    formData.append('totalChunks', totalChunks);

                    try {
                        const response = await fetch("{{ route('uploadchunk_api') }}", {
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer ' + token
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (result.status === 'error') {
                            console.error('Failed to upload chunk:', result.message);
                            return;
                        }

                        // อัพเดท progress bar
                        const progressBar = document.querySelector('.progress-bar');
                        if (progressBar) {
                            const progress = Math.round(((currentChunk + 1) / totalChunks) * 100);
                            progressBar.style.width = progress + '%';
                            progressBar.textContent = progress + '%';
                        }

                        currentChunk++;
                    } catch (error) {
                        console.error('Error uploading chunk:', error);
                        return;
                    }
                }

                console.log('File uploaded successfully:', file.name);
            }

            // รีเซ็ตสถานะการอัพโหลด
            function resetUpload() {
                uploadInProgress = false;
                currentChunk = 0;
                currentFile = null;
                // รีเซ็ต progress bar
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = '0%';
                    progressBar.textContent = '0%';
                }
            }

            $('#browseButton').click(() => fileInput.click());

            dropzone.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-blue-500');
            }).on('dragleave', function() {
                $(this).removeClass('border-blue-500');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-500');
                handleFiles(e.originalEvent.dataTransfer.files);
            });

            function handleFiles(files) {
                uploadFiles(files);
            }
        });
    </script>
@endsection
