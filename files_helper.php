<?php

if (!function_exists('get_upload_folder')) {
    function get_upload_folder($user)
    {
        if ($user == 'user/') {
            $path = ROOTPATH . 'public/uploads/' . $user . sha1(session('uid')) . '/';
        } else {
            $path = ROOTPATH . 'public/uploads/' . $user . sha1(session('sid')) . '/';
        }
        if (!is_dir($path)) {
            $uold = umask(0);
            mkdir($path, 0777, true);
            umask($uold);
            file_put_contents($path . "index.html", "<h1>404 Not Found</h1>");
        }
        return $path;
    }
}


if (!function_exists('get_link_file')) {
    function get_link_file($file_name, $user)
    {
        if ($user == 'user/') {
            $path = 'public/uploads/' . $user. sha1(session('uid')) . '/';
        } else {
            $path = 'public/uploads/' . $user. sha1(session('sid')) . '/';
        }
        return $path . $file_name;
    }
}
if (!function_exists('search_file_in_directory')) {
    function search_file_in_directory($directory, $filename)
    {
        $directory = rtrim($directory, '/') . '/';

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }

        return false;
    }
}
if (!function_exists('get_name_folders_from_dir')) {
    function get_name_folders_from_dir($dir = "")
    {
        $dir = $dir ?: APPPATH . "Modules/Blocks/Addons/";

        return array_map('basename', glob($dir . '/*', GLOB_ONLYDIR)) ?: [];
    }
}

if (!function_exists('get_name_files_from_dir')) {
    function get_name_files_from_dir($dir = "")
    {
        if ($dir == "") {
            $dir = FCPATH . "Modules/Blocks/Addons/";
        }

        $files = scandir($dir);

        // Filter out "." and ".." entries and directories
        $files = array_filter($files, function ($file) use ($dir) {
            return is_file($dir . '/' . $file);
        });

        $filePaths = array_map(function ($file) use ($dir) {
            return realpath($dir . '/' . $file);
        }, $files);

        $fileNames = array_map(function ($filePath) {
            return pathinfo($filePath, PATHINFO_FILENAME);
        }, $filePaths);

        return array_values($fileNames);
    }
}

function get_json_content_from_file($filePath)
{
    $fileContents = file_get_contents($filePath);

    if ($fileContents === false) {
        return null;
    }
    $jsonData = json_decode($fileContents, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return null; 
    }

    return $jsonData;
}



function searchFileInFolder($folderPath, $fileName) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderPath),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === $fileName) {
            return $file->getPathname(); 
        }
    }

    return false; 
}

function __curl($url, $zipPath = "")
{
    $zipResource = fopen($zipPath, "w");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FILE, $zipResource);
    $page = curl_exec($ch);
    if (!$page) {
        ms([
            "status" => "error",
            "message" => "Error :- " . curl_error($ch),
        ]);
    }
    curl_close($ch);
}

if (!function_exists("__inst")) {
    function _inst($result)
    {
        if (empty($result)) {
            ms([
                "status" => "error",
                "message" => 'There was an error processing your request. Please contact the author for support.',
            ]);
        }
        if ((isset($result->status) && $result->status == 'error')) {
            ms([
                "status" => "error",
                "message" => $result->message,
            ]);
        }
        if (isset($result->status) && $result->status == 'success') {
            $result_object = explode("{|}", $result->response);
            $file_path = 'files.zip';
            __curl(base64_decode($result_object[2]), $file_path);
            if (filesize($file_path) <= 1) {
                ms([
                    "status" => "error",
                    "message" => "There was an error processing your request. Please contact the author for support.",
                ]);
            }
            $zip = new \ZipArchive();
            if ($zip->open($file_path) !== true) {
                ms([
                    "status" => "error",
                    "message" => "Error :- Unable to open the Zip File",
                ]);
            }
            $zip->extractTo(ROOTPATH); // Use the appropriate path
            $zip->close();
            @unlink($file_path);
            return $result_object;
        }
    }
}

function extract_zip_file($zipFile, $output_filename)
{
    $zip = new \ZipArchive();
    $extractPath = $output_filename;
    if ($zip->open($zipFile) !== true) {
        ms([
            "status" => "error",
            "message" => "Error :- Unable to open the Zip File",
        ]);
    }
    $zip->extractTo($extractPath);
    $zip->close();
    ms([
        "status" => "success",
        "message" => "Extracted All Files",
    ]);
}