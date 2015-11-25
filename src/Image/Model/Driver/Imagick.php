<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 23.11.2015
 * Time: 14:39
 */

namespace Image\Model\Driver;


use DeltaCore\Parts\Configurable;
use HttpWarp\Header;

class Imagick extends AbstractDriver
{
    use Configurable;
    /**
     * Imagick image object
     * @var \Imagick
     */
    protected $image;

    public function read($file)
    {
        $this->image = new \Imagick();
        $this->image->readImage($file);
    }

    protected function getImage()
    {
        return $this->image;
    }

    public function getWidth()
    {
        return $this->getImage()->getImageWidth();
    }

    public function getHeight()
    {
        return $this->getImage()->getImageHeight();
    }

    public function getResizeFilter()
    {
        $this->getConfig(["resize", "filter"], \Imagick::FILTER_LANCZOS);
    }

    public function resize($width = null, $height = null)
    {
        $width = $width ?: $this->calcWidth($height);
        $height = $height ?: $this->calcHeight($width);

        return $this->getImage()->resizeImage($width, $height, $this->getResizeFilter(), 1);
    }


    public function crop($width = null, $height = null)
    {
        $width = $width ?: $this->calcWidth($height);
        $height = $height ?: $this->calcHeight($width);

        $x = ($this->getWidth() - $width) / 2;
        $y = ($this->getHeight() - $height) / 2;

        $this->getImage()->cropImage($width, $height, $x, $y);
        $this->getImage()->setImagePage($width, $height, 0, 0);
    }

    public function resizeAndCrop($width, $height)
    {
        $widthR = $this->calcWidth($height);
        $heightR = $this->calcHeight($width);

        if ($widthR > $width + 1) {
            $this->resizeToWidth($widthR);
        } elseif ($heightR > $height + 1) {
            $this->resizeToHeight($heightR);
        } else {
            $this->resize($widthR, $heightR);
        }

        return $this->crop($width, $height);
    }

    public function show()
    {
        $this->getImage()->setImageFormat('jpg');
        header('Content-Type: image/' . $this->getImage()->getImageFormat());
        echo $this->getImage()->getimageblob();
    }

    public function addWatermarkText(
        $text = "Copyright",
        $font = "Courier",
        $size = 20,
        $color = "black",
        $maskColor = "white",
        $position = \Imagick::GRAVITY_SOUTHEAST
    )
    {
        $draw = new \ImagickDraw();

        $draw->setFont($font);
        $draw->setFontSize($size);
        $draw->setFillColor($color);

        $draw->setGravity($position);

        $this->getImage()->annotateImage($draw, 10, 12, 0, $text);

        $draw->setFillColor($maskColor);
        return $this->getImage()->annotateImage($draw, 11, 11, 0, $text);
    }

    public function addWatermarkTextMask(
        $text = "Copyright",
        $font = "Courier",
        $size = 20,
        $color = "grey70",
        $maskColor = "grey30",
        $position = \Imagick::GRAVITY_SOUTHEAST
    )
    {
        $watermark = new \Imagick();
        $mask = new \Imagick();
        $draw = new \ImagickDraw();

        $width = $this->getWidth();
        $height = $this->getHeight();

        $watermark->newImage($width, $height, new \ImagickPixel($maskColor));
        $mask->newImage($width, $height, new \ImagickPixel('black'));

        $draw->setFont($font);
        $draw->setFontSize($size);
        $draw->setFillColor($color);

        $draw->setGravity($position);

        $watermark->annotateImage($draw, 10, 12, 0, $text);

        $draw->setFillColor('white');
        $mask->annotateImage($draw, 11, 13, 0, $text);
        $mask->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor('black');
        $mask->annotateImage($draw, 9, 11, 0, $text);

        $mask->setImageMatte(false);

        $watermark->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        return $this->getImage()->compositeImage($watermark, \Imagick::COMPOSITE_DISSOLVE, 0, 0);
    }

    public function addWatermarkTextMosaic(
        $text = "Copyright",
        $font = "Courier",
        $size = 20,
        $color = "grey70",
        $maskColor = "grey30",
        $position = \Imagick::GRAVITY_SOUTHEAST
    )
    {
        $watermark = new \Imagick();

        $draw = new \ImagickDraw();
        $watermark->newImage(140, 80, new \ImagickPixel('none'));

        $draw->setFont($font);
        $draw->setFillColor('grey');
        $draw->setFillOpacity(.4);

        $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);

        $watermark->annotateImage($draw, 10, 10, 0, $text);

        $draw->setGravity(\Imagick::GRAVITY_SOUTHEAST);

        $watermark->annotateImage($draw, 5, 15, 0, $text);

        for ($w = 0; $w < $this->getImage()->getImageWidth(); $w += 140) {
            for ($h = 0; $h < $this->getImage()->getImageHeight(); $h += 80) {
                $this->getImage()->compositeImage($watermark, \Imagick::COMPOSITE_OVER, $w, $h);
            }
        }
    }
}
