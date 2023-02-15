<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Upload;

class ProductController extends Controller
{
    public function store()
    {
        $image = $this->input->file('image');

        $validator = Validator::validate([
            'image' => [
                'required' => true,
                'file' => true,
            ],
        ], [
            'image' => $image
        ]);

        if ($validator->fails()) {
            $this->response(400, [
                'status' => 'error',
                'message' => 'Validation failed',
                'data' => $validator->errors(),
            ]);
        }

        $upload = new Upload($image);
        $upload->setPath($_SERVER['DOCUMENT_ROOT'] . "/uploads/");
        $upload->setAllowedExtensions(['jpg','png','jpeg','JPG']);
        $upload->setAllowedMimeTypes(['image/jpeg', 'image/png']);

        $upload->setRandomName(true);
        $upload->validate();
        $upload->upload();
        if ($upload->isUploaded()) {
            $upload->move();
            $this->response(200, [
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => [
                    'file' => $upload->getName(),
                    'path' => $upload->getPath(),
                ],
            ]);
        }
        $this->response(400, [
            'status' => 'error',
            'message' => 'File not uploaded',
            'data' => [
                'file' => $upload->getName(),
                'path' => $upload->getPath(),
            ],
        ]);
    }
}
