<?php
namespace App\Controllers;

use App\Models\FileManagerModel;
use CodeIgniter\Controller;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;

class File_manager extends Controller
{
    protected $tb_file_manage;
    protected $model;
    protected $controller_name;
    protected $image;

    public function __construct()
    {
        helper('files');
        $this->model = new FileManagerModel();
        $this->tb_file_manage = 'file_manager'; // Adjust this based on your actual table name
        $this->controller_name = strtolower(get_class($this));
        $this->image = \Config\Services::image(); // Injected Image library
    }

    public function upload_files()
    {
        _is_ajax();
    
        $user = post('user') . '/';
        $path = get_upload_folder($user);
    
        if ($user == 'user/') {
            $uid = session('uid');
        } else {
            $uid = '';
        }
    
        $validation = \Config\Services::validation();
        if(post('type')=='image'){
            $validation->setRules([
                'files' => [
                    'rules' => [
                        'uploaded[files]',
                        'is_image[files]',
                        'mime_in[files,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                        'max_size[files,1024]',
                        'max_dims[files,2024,2024]',
                    ],
                ],
            ]);
        }else{
            $validation->setRules([
                'files' => [
                    'rules' => 'uploaded[files]|mime_in[files,application/zip]',
                    'label' => 'ZIP File',
                ],
            ]);            
        }
    
        if (!$validation->withRequest($this->request)->run()) {
            $message = '';
            foreach ($validation->getErrors() as $va) {
                $message .= $va . '<br>';
            }
            ms(['status' => 'error', 'message' => $message]);
        }

        if($this->request->getFileMultiple('files'))
        {
            $files = $this->request->getFileMultiple('files');
 
            foreach ($files as $file) {
 
                if ($file->isValid() && ! $file->hasMoved())
                {
                    $newName = $file->getRandomName();
                    $file->move($path, $newName);
                    if(post('type')=='image'){
                        $this->resizeImage($path . $newName, $path, $newName, 60);
                    }
                    $data = [
                        "uid"          => $uid,
                        "file_name"    => $newName,
                        "file_url"     => get_link_file($newName, $user),
                        "file_type"    => $file->getClientMimeType(), 
                        "file_size"    => $file->getSize(),
                        "created_at"      => Time::now(),
                    ];
                    $this->model->insert($data);
    
                    ms([
                        "status"  => "success",
                        "link"    => get_link_file($newName, $user),
                        "message" => 'Media uploaded successfully...',
                    ]);

                }
                 
            }
 
        }
    }
    
    
    private function resizeImage($tempFilePath, $destinationPath, $fileName, $quality = 60)
    {
        $this->image = \Config\Services::image();
        $this->image->withFile($tempFilePath)
                    ->save($destinationPath . '/' . $fileName, $quality); 
        $this->image->withFile($tempFilePath)
            ->save($destinationPath . '/' . $fileName, $quality);
    }

    // Controller method for handling file uploads from the TinyMCE file browser callback
    public function upload_files_tiny()
    {
        _is_ajax();
    
        $user = post('user') . '/';
        $path = get_upload_folder($user);
    
        if ($user == 'user/') {
            $uid = session('uid');
        } else {
            $uid = '';
        }

        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($path, $newName);

            $this->resizeImage($path . $newName, $path, $newName, 30);
            $data = [
                "uid"          => $uid,
                "file_name"    => $newName,
                "file_url"     => get_link_file($newName, $user),
                "file_type"    => $file->getClientMimeType(), 
                "file_size"    => $file->getSize(),
                "created_at"      => Time::now(),
            ];
            $this->model->insert($data);

            $uploadedUrl = base_url(get_link_file($newName,$user));
            return $this->response->setJSON(['url' => $uploadedUrl]);
        } else {
            // Log or return the error
            $error = $file->getErrorString() . '(' . $file->getError() . ')';
            return $this->response->setJSON(['error' => 'File upload failed. ' . $error]);
        }

    }

    
    public function view_files($id=''){
        _is_ajax();
        $data['item'] =  $this->model->get('*','kyc',['ids'=>$id],'','',true);
        if(!empty($data['item'])){
            echo view('layouts/common/modal1/files_reader',$data);
        }
    }

}