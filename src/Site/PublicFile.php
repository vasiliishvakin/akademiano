<?php


namespace Akademiano\Sites\Site;


use Akademiano\HttpWarp\Header;

class PublicFile extends File
{
    protected $webPath;

    /**
     * @return mixed
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * @param mixed $webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
    }

    public function sendContent()
    {
        Header::accel($this->getWebPath(), $this->getPath());
    }
}
