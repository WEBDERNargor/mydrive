@extends('user.layout')
@section("head")
<title>Upload - {{NAME()}}</title>
@endsection
@section('content')

<div class="mt-20 max-w-2xl mx-auto ">
    <form id="uploadForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
       
        <div class="w-full h-64 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center relative" id="dropzone">
            <div class="text-center">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Drag and drop files here or</p>
                <button type="button" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" id="browseButton">
                    Browse Files
                </button>
                <input type="file" multiple class="hidden" id="fileInput" name="files[]" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.mp4,.mov,.wmv,.flv,.zip,.rar">
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
@section("scripts")
<script>
$(document).ready(function() {
    const dropzone = $('#dropzone');
    const fileInput = $('#fileInput');
    const fileList = $('#fileList');
    const errorMessages = $('#errorMessages');
    const chunkSize = 2 * 1024 * 1024; // 2MB per chunk

    const allowedTypes = [
        'application/zip',
        'application/x-rar-compressed',
        'image/gif', // MIME type สำหรับ gif
        'image/png', // MIME type สำหรับ png
        'image/jpeg', // MIME type สำหรับ jpeg
        'video/mp4', // MIME type สำหรับ mp4
        'video/quicktime', // MIME type สำหรับ mov
        'video/x-ms-wmv', // MIME type สำหรับ wmv
        'video/x-flv' // MIME type สำหรับ flv
    ];

    const maxFileSize = 2048 * 1024 * 1024; // 2GB
    let selectedFiles = []; // Array to store selected files
    let filesUploaded = 0; // Counter for uploaded files

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
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <div data-key='${index}' class="flex items-center justify-between p-2 bg-gray-100 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-file mr-2"></i>
                            <span>${file.name}</span>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
                            <div class="progress-bar h-2 bg-green-500 rounded-full" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                `);
                preview.find('button').click(function() {
                    let key = Number(preview.data('key'));
                    selectedFiles = selectedFiles.filter((_, i) => i !== key);
                    preview.remove();
                });
                fileList.append(preview);
            };
            reader.readAsDataURL(file);
        });
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

    $("#uploadForm").submit(function(e){
        e.preventDefault();
        if (fileList.children().length === 0) {
            errorMessages.html('<p>Please select at least one file</p>');
            return false;
        }

        filesUploaded = 0; // Reset counter
        selectedFiles.forEach((file, index) => uploadFileInChunks(file, index));
    });

    function uploadFileInChunks(file, index) {
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
                processData: false,
                contentType: false,
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            let percentComplete = (start + e.loaded) / file.size * 100;
                            $(`#fileList > div[data-key='${index}'] .progress-bar`).css('width', percentComplete + '%').attr('aria-valuenow', percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
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
                                confirmButtonText: 'OK'
                            });
                        }
                        console.log('Upload complete:', response);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Upload failed:', textStatus, errorThrown);
                }
            });
        }

        uploadNextChunk();
    }
});
</script>
@endsection
