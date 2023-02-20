<?php

namespace App\Core;

class Upload
{
    protected $file;
    protected $path;
    protected $name;
    protected $extension;
    protected $size;
    protected $mimeType;
    protected $error;
    protected $allowedExtensions;
    protected $allowedMimeTypes;
    protected $maxSize;
    protected $overwrite;
    protected $randomName;
    protected $newName;
    protected $isUploaded;
    protected $isMoved;
    protected $isImage;
    protected $imageWidth;
    protected $imageHeight;
    protected $imageType;
    protected $imageSizeStr;
    protected $imageMime;
    protected $imageExtension;


    public function __construct($file)
    {
        $this->file = $file;
        $this->path = __DIR__ . '/../../public/uploads/';
        $this->name = $file['name'] ?? '';
        $this->extension = pathinfo($this->name, PATHINFO_EXTENSION);
        $this->size = $file['size'] ?? 0;
        $this->mimeType = $file['type'] ?? '';
        $this->error = $file['error'] ?? 0;
        $this->allowedExtensions = [];
        $this->allowedMimeTypes = [];
        $this->maxSize = 0;
        $this->overwrite = false;
        $this->randomName = false;
        $this->newName = null;
        $this->isUploaded = false;
        $this->isMoved = false;
        $this->isImage = false;
        $this->imageWidth = 0;
        $this->imageHeight = 0;
        $this->imageType = 0;
        $this->imageSizeStr = '';
        $this->imageMime = '';
        $this->imageExtension = '';
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    public function setAllowedMimeTypes($allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }

    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
        return $this;
    }

    public function setRandomName($randomName)
    {
        $this->randomName = $randomName;
        return $this;
    }

    public function setNewName($newName)
    {
        $this->newName = $newName;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getOverwrite()
    {
        return $this->overwrite;
    }

    public function getRandomName()
    {
        return $this->randomName;
    }

    public function getNewName()
    {
        return $this->newName;
    }

    public function isUploaded()
    {
        return $this->isUploaded;
    }

    public function isMoved()
    {
        return $this->isMoved;
    }


    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    public function getImageType()
    {
        return $this->imageType;
    }

    public function getImageSizeStr()
    {
        return $this->imageSizeStr;
    }

    public function getImageMime()
    {
        return $this->imageMime;
    }

    public function getImageExtension()
    {
        return $this->imageExtension;
    }

    public static function upload()
    {
        return new static($_FILES);
    }

    public function validate()
    {
        if ($this->error !== 0) {
            throw new \Exception('Error uploading file');
        }

        if (!empty($this->allowedExtensions) && !in_array($this->extension, $this->allowedExtensions)) {
            throw new \Exception('Extension not allowed');
        }

        if (!empty($this->allowedMimeTypes) && !in_array($this->mimeType, $this->allowedMimeTypes)) {
            throw new \Exception('Mime type not allowed');
        }

        if ($this->maxSize > 0 && $this->size > $this->maxSize) {
            throw new \Exception('File size exceeds limit');
        }

        if (!is_dir($this->path)) {
            throw new \Exception('Directory does not exist');
        }

        if (!is_writable($this->path)) {
            throw new \Exception('Directory is not writable');
        }

        if (file_exists($this->path . $this->name) && !$this->overwrite) {
            throw new \Exception('File already exists');
        }

        $this->isUploaded = true;
        return $this;
    }

    public function move()
    {
        if (!$this->isUploaded) {
            throw new \Exception('File not uploaded');
        }

        if ($this->randomName) {
            $this->name = uniqid() . '.' . $this->extension;
        }

        if ($this->newName) {
            $this->name = $this->newName . '.' . $this->extension;
        }

        // check if tmp_name directory exists
        if (!file_exists($this->file['tmp_name'])) {
            throw new \Exception('File not found');
        }

        // upload file from cross server
        if (is_uploaded_file($this->file['tmp_name'])) {
            if (!move_uploaded_file($this->file['tmp_name'], $this->path . $this->name)) {
                throw new \Exception('Error moving file');
            }
        }   // upload file from same server
        else {
            if (!rename($this->file['tmp_name'], $this->path . $this->name)) {
                throw new \Exception('Error moving file');
            }
        }

        $this->isMoved = true;
        return $this;
    }

    public function getImageInfo()
    {
        if (!$this->isImage) {
            throw new \Exception('File is not an image');
        }

        $imageInfo = getimagesize($this->file['tmp_name']);
        $this->imageWidth = $imageInfo[0];
        $this->imageHeight = $imageInfo[1];
        $this->imageType = $imageInfo[2];
        $this->imageSizeStr = $imageInfo[3];
        $this->imageMime = $imageInfo['mime'];
        $this->imageExtension = image_type_to_extension($imageInfo[2], false);
        return $this;
    }

    public function isImage()
    {
        $this->isImage = getimagesize($this->file['tmp_name']) !== false;
        return $this;
    }
}
