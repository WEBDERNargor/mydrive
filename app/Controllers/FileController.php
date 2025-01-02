<?php

namespace App\Controllers;
use App\Controllers\ServiceController;
use App\Models\Custom;
use Intervention\Image\ImageManagerStatic as Image;
use PDO;
class FileController
{
    private $uploadPath;
    private $service;
    private $sql;

    public function __construct()
    {
        global $app;
        $this->uploadPath = __DIR__ . '/../../storage/uploads/';
        $this->service = new ServiceController();
        $this->sql = new Custom($app->db);
        // Create uploads directory if it doesn't exist
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
        // ทำความสะอาดไฟล์ .part ที่เก่า
        $this->cleanupOldPartFiles();
    }



    public function get_file_data()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        if (!$token) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบ token"], 401);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $this->service->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "token ไม่ถูกต้องหรือหมดอายุ"], 401);

        }
        $res = $this->sql->param("SELECT * FROM files WHERE u_id=?", [$user['u_id']]);
        $fetch = $res->fetchAll(PDO::FETCH_ASSOC);
        return $this->jsonResponse(["status" => "success", "message" => "load data successfully", "data" => $fetch], 201);
    }


    /**
     * ลบไฟล์ .part ที่เก่าเกิน 24 ชั่วโมง
     */
    private function cleanupOldPartFiles()
    {
        try {
            $files = glob($this->uploadPath . '*/*.part');
            $currentTime = time();
            $maxAge = 24 * 60 * 60; // 24 ชั่วโมง

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileAge = $currentTime - filemtime($file);
                    if ($fileAge > $maxAge) {
                        unlink($file);
                        error_log("Cleaned up old .part file: " . $file);
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Error cleaning up .part files: " . $e->getMessage());
        }
    }

    /**
     * ลบไฟล์ .part ทั้งหมดของผู้ใช้
     */
    private function cleanupAllUserPartFiles($userId)
    {
        try {
            $files = glob($this->uploadPath . $userId . '_*.part');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    error_log("Cleaned up user .part file: " . $file);
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log("Error cleaning up user .part files: " . $e->getMessage());
            return false;
        }
    }

    public function uploadChunk()
    {
        header("Content-type: application/json; charset=utf-8");
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        $user = $this->service->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
            exit();
        }

        $fileName = $_POST['fileName'];
        $chunkIndex = $_POST['chunkIndex'];
        $totalChunks = $_POST['totalChunks'];

        $uploadDir = $this->uploadPath;
        $tempFilePath = $uploadDir . $user['u_id'] . '_' . $fileName . '.part';

        // ลบไฟล์เก่าเฉพาะตอนเริ่มอัพโหลดไฟล์ใหม่
        if ($chunkIndex === '0') {
            // ทำความสะอาดไฟล์ .part เก่าของผู้ใช้
            $this->cleanupAllUserPartFiles($user['u_id']);
            // เริ่มไฟล์ใหม่
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }

        // Append chunk to the temporary file
        $chunk = file_get_contents($_FILES['file']['tmp_name']);
        if (file_put_contents($tempFilePath, $chunk, FILE_APPEND) === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to write chunk']);
            exit();
        }

        // If all chunks are uploaded, rename the temporary file
        if ($chunkIndex == $totalChunks - 1) {

            $getfilearr = explode(".", $fileName);
            $type = end($getfilearr);
            $uniqueFileName = uniqid();
            $new_name = $user['u_id'] . '_' . $uniqueFileName;
            $new_filename = $new_name . '.' . $type;
            $finalFilePath = $uploadDir . $new_filename;

            // ตรวจสอบว่าไฟล์ชั่วคราวมีอยู่จริง
            if (!file_exists($tempFilePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Temporary file not found']);
                exit();
            }

            // ย้ายไฟล์และตั้งค่าสิทธิ์
            if (rename($tempFilePath, $finalFilePath)) {
                chmod($finalFilePath, 0644); // ตั้งสิทธิ์ให้อ่านได้ทั่วไป แต่แก้ไขได้เฉพาะเจ้าของ
                $res = $this->sql->param("INSERT INTO files (u_id, file_name,file_raw_name,file_ext) VALUES (?,?,?,?)", [$user['u_id'], $fileName, $new_name, $type]);

                // สร้าง thumbnail สำหรับวิดีโอ
                $videoTypes = ['mp4', 'mov', 'wmv', 'flv'];
                if (in_array(strtolower($type), $videoTypes)) {
                    try {
                        $thumbnailPath = $uploadDir . 'thumbnails/';
                        if (!file_exists($thumbnailPath)) {
                            mkdir($thumbnailPath, 0755, true);
                        }

                        // ใช้ ffmpeg เพื่อสร้าง thumbnail จากวิดีโอ
                        $thumbnailFile = $thumbnailPath . $new_name . '.png';
                        $ffmpegCommand = "ffmpeg -i " . escapeshellarg($finalFilePath) . " -ss 00:00:02 -vframes 1 -vf scale=200:200:force_original_aspect_ratio=decrease " . escapeshellarg($thumbnailFile);
                        exec($ffmpegCommand, $output, $returnCode);

                        if ($returnCode === 0) {
                            // อัพเดทฐานข้อมูลว่ามี thumbnail
                            $this->sql->param("UPDATE files SET has_thumbnail = 1 WHERE file_raw_name = ?", [$new_name]);
                        } else {
                            error_log("Error creating video thumbnail: " . implode("\n", $output));
                        }
                    } catch (\Exception $e) {
                        error_log("Error creating video thumbnail: " . $e->getMessage());
                    }
                }

                echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully', 'fileName' => $fileName]);
                exit();
            }

        }

        echo json_encode(['status' => 'success', 'message' => 'Chunk uploaded successfully']);
        exit();
    }


    public function upload()
    {

        // pre_r($_REQUEST);
        // if(isset($_FILES)){
        // pre_r($_FILES);
        // }

        $response = ['status' => 'success', 'files' => [], 'message' => 'File uploaded successfully'];
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

    public function getFile($filename, $filetype)
    {
        $filePath = $this->uploadPath . $filename . "." . $filetype;

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

    public function getThumnail($filename)
    {
        $filePath = $this->uploadPath . "thumbnails/" . $filename . ".png";

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

    public function streamVideo($filename, $filetype)
    {
        $filePath = $this->uploadPath . $filename . "." . $filetype;

        if (!file_exists($filePath)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $fileSize = filesize($filePath);
        $offset = 0;
        $length = $fileSize;

        if (isset($_SERVER['HTTP_RANGE'])) {
            // Extract the range header
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $offset = intval($matches[1]);

            if (isset($matches[2])) {
                $length = intval($matches[2]) - $offset + 1;
            } else {
                $length = $fileSize - $offset;
            }

            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $offset-" . ($offset + $length - 1) . "/$fileSize");
        } else {
            header('HTTP/1.1 200 OK');
        }

        // Set headers for video streaming
        header('Content-Type: video/' . $filetype);
        header('Content-Length: ' . $length);
        header('Accept-Ranges: bytes');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        // Open file for reading
        $file = fopen($filePath, 'rb');

        // Seek to the requested offset
        fseek($file, $offset);

        // Stream the video in chunks
        $chunkSize = 8192; // 8KB chunks
        $bytesRemaining = $length;

        while ($bytesRemaining > 0 && !feof($file)) {
            $bytesToRead = min($chunkSize, $bytesRemaining);
            $data = fread($file, $bytesToRead);
            echo $data;
            flush();
            $bytesRemaining -= strlen($data);
        }

        fclose($file);
        exit;
    }

    /**
     * ลบไฟล์ .part เมื่อการอัพโหลดถูกยกเลิกหรือเน็ตหลุด
     */
    public function cleanupUpload()
    {
        header("Content-type: application/json; charset=utf-8");
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        $user = $this->service->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
            exit();
        }

        $fileName = $_POST['fileName'] ?? null;
        if (!$fileName) {
            echo json_encode(['status' => 'error', 'message' => 'Filename is required']);
            exit();
        }

        $tempFilePath = $this->uploadPath . $user['u_id'] . '_' . $fileName . '.part';
        if (file_exists($tempFilePath)) {
            if (unlink($tempFilePath)) {
                echo json_encode(['status' => 'success', 'message' => 'Upload cleaned up successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to cleanup upload']);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'No file to cleanup']);
        }
        exit();
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        header("Content-type: application/json; charset=utf-8");
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function deletefile_api()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        if ($token == null) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบ token"], 401);
        }
        $token = str_replace('Bearer ', '', $token);

        $user = $this->service->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "token ไม่ถูกต้องหรือหมดอายุ"], 401);
        }
        parse_str(file_get_contents("php://input"), $data);

        if (!isset($data['id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่ได้รับค่าไอดีของไฟล์"], 401);
        }
        $id = $data['id'];
        $res = $this->sql->param("SELECT * FROM files WHERE u_id=? AND file_id=?", [$user['u_id'], $id]);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!isset($row['file_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบไฟล์"], 401);
        }
        if (file_exists($this->uploadPath . $row['file_raw_name'] . "." . $row['file_ext'])) {
            unlink($this->uploadPath . $row['file_raw_name'] . "." . $row['file_ext']);
        }
        if ($row['has_thumbnail'] == 1) {
            if (file_exists($this->uploadPath . "thumbnails/" . $row['file_raw_name'] . ".png")) {
                unlink($this->uploadPath . "thumbnails/" . $row['file_raw_name'] . ".png");
            }
        }
        $res_delete = $this->sql->param("DELETE FROM files WHERE u_id=? AND file_id=?", [$user['u_id'], $row['file_id']]);
        if ($res_delete) {
            return $this->jsonResponse(["status" => "success", "message" => "ลบไฟล์สำเร็จ"], 201);
        } else {
            return $this->jsonResponse(["status" => "error", "message" => "ลบไม่สำเร็จ"], 500);
        }

    }

    public function updatefilepublic_api()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        if ($token == null) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบ token"], 401);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $this->service->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "token ไม่ถูกต้องหรือหมดอายุ"], 401);
        }
        parse_str(file_get_contents("php://input"), $data);
        if (!isset($data['id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่ได้รับค่าไอดีของไฟล์"], 401);
        }
        $id = $data['id'];
        $res = $this->sql->param("SELECT * FROM files WHERE u_id=? AND file_id=?", [$user['u_id'], $id]);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!isset($row['file_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบไฟล์"], 401);
        }


        $res_update = $this->sql->param("UPDATE files SET file_public=? WHERE u_id=? AND file_id=?", [$data['is_public'], $user['u_id'], $row['file_id']]);
        if ($res_update) {
            return $this->jsonResponse(["status" => "success", "message" => "แก้ไขสำเร็จ"], 201);
        } else {
            return $this->jsonResponse(["status" => "error", "message" => "แก้ไขไมม่สำเร็จ"], 500);
        }
    }

}
