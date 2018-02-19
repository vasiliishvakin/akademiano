<?php


namespace Akademiano\Attach\Model;


use Akademiano\HttpWarp\File\UploadFile;

class UploadFileInfo extends RequestFileInfo
{
    /** @var UploadFile */
    protected $uploadedFile;

    /**
     * @return UploadFile
     */
    public function getUploadedFile(): UploadFile
    {
        return $this->uploadedFile;
    }

    /**
     * @param UploadFile $uploadedFile
     */
    public function setUploadedFile(UploadFile $uploadedFile): void
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['uploadedFile'] = $this->getUploadedFile();
        return $data;
    }
}
