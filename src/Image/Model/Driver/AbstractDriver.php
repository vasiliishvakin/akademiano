<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 23.11.2015
 * Time: 16:01
 */

namespace Image\Model\Driver;


abstract class AbstractDriver
{

    abstract public function resize($width = null, $height = null);

    abstract public function crop($width = null, $height = null);

    abstract public function getWidth();

    abstract public function getHeight();

    public function calcWidth($height)
    {
        $scale = $this->getHeight() / $height;
        $width = round($this->getWidth() / $scale);

        return $width;
    }

    public function calcHeight($width)
    {
        $scale = $this->getWidth() / $width;
        $height = round($this->getHeight() / $scale);

        return $height;
    }

    public function resizeToWidth($width)
    {
        return $this->resize($width);
    }

    public function resizeToHeight($height)
    {
        return $this->resize(null, $height);
    }

}
