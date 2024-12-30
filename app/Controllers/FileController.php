<?php

namespace App\Controllers;



class FileController
{
    private $uploadPath;

    public function __construct()
    {
        $this->uploadPath =  __DIR__.'/../../storage/uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }



    public function uploadChunk()
    {
        $fileName = $_POST['fileName'];
        $chunkIndex = $_POST['chunkIndex'];
        $totalChunks = $_POST['totalChunks'];

        $uploadDir = $this->uploadPath;
        $tempFilePath = $uploadDir . $fileName . '.part';

        // Append chunk to the temporary file
        $chunk = file_get_contents($_FILES['file']['tmp_name']);
        file_put_contents($tempFilePath, $chunk, FILE_APPEND);

        // If all chunks are uploaded, rename the temporary file
        if ($chunkIndex == $totalChunks - 1) {
            $uniqueFileName = uniqid() . '-' . $fileName;
            $finalFilePath = $uploadDir . $uniqueFileName;
            rename($tempFilePath, $finalFilePath);
            return json_encode(['status' => 'success', 'message' => 'File uploaded successfully', 'fileName' => $uniqueFileName]);
        }

        return json_encode(['status' => 'success', 'message' => 'Chunk uploaded successfully']);
    }


    public function upload()
    {

        // pre_r($_REQUEST);
        // if(isset($_FILES)){
        // pre_r($_FILES);
        // }

        $response = ['status' => 'success', 'files' => [], 'message' => ''];

        if (empty($_FILES['files'])) {
            return json_encode(['status' => 'error', 'message' => 'No files uploaded']);
        }

        $files = $this->reArrayFiles($_FILES['files']);

        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $uniqueName = uniqid() . '_' . basename($file['name']);
                $targetPath = $this->uploadPath . $uniqueName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $response['files'][] = [
                        'originalName' => $file['name'],
                        'fileName' => $uniqueName,
                        'size' => $file['size'],
                        'type' => $file['type'],
                        'url' => '/file/' . $uniqueName
                    ];
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to upload some files';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Some files had upload errors';
            }
        }

        if (empty($response['message'])) {
            $response['message'] = count($response['files']) . ' files uploaded successfully';
        }

        return json_encode($response);
     
    }

    private function reArrayFiles($files)
    {
        $fileArray = [];
        $fileCount = count($files['name']);
        $fileKeys = array_keys($files);

        for ($i = 0; $i < $fileCount; $i++) {
            foreach ($fileKeys as $key) {
                $fileArray[$i][$key] = $files[$key][$i];
            }
        }

        return $fileArray;
    }

    public function getFile($filename)
    {
        $filePath = $this->uploadPath . $filename;
        
        if (!file_exists($filePath)) {
            header("HTTP/1.0 404 Not Found");
            exit('File not found');
        }

        // Validate that the file is within the uploads directory
        $realPath = realpath($filePath);
        if (strpos($realPath, realpath($this->uploadPath)) !== 0) {
            header("HTTP/1.0 403 Forbidden");
            exit('Access denied');
        }

        $mimeType = mime_content_type($filePath);
        header("Content-Type: " . $mimeType);
        header("Content-Disposition: inline; filename=\"" . basename($filename) . "\"");
        header("Content-Length: " . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
}
