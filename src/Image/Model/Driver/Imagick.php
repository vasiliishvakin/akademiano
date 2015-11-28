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
use Image\Model\Watermark;

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
        $this->setFile($file);
    }

    protected function getImage()
    {
        if (null === $this->image) {
            $this->image = new \Imagick();
            $this->image->readImage($this->getFile());
        }
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
        header('Content-Type: image/' . $this->getImage()->getImageFormat());
        echo $this->getImage()->getimageblob();
    }

    public function addWatermark(Watermark $watermark)
    {
        switch($watermark->getMode()) {
            case Watermark::MODE_TEXT:
                return $this->addWatermarkText($watermark);
            case Watermark::MODE_TEXT_MASK:
                return $this->addWatermarkTextMask($watermark);
            case Watermark::MODE_TEXT_MOSAIC:
                return $this->addWatermarkTextMosaic($watermark);

        }

    }

    public function addWatermarkText(Watermark $watermark)
    {
        $draw = new \ImagickDraw();

        $draw->setFont($watermark->getFont());
        $draw->setFontSize($watermark->getSize());
        $draw->setFillColor($watermark->getColor());

        $draw->setGravity($watermark->getPosition());

        $this->getImage()->annotateImage($draw, 10, 12, 0, $watermark->getText());

        $draw->setFillColor($watermark->getMaskColor());
        return $this->getImage()->annotateImage($draw, 11, 11, 0, $watermark->getText());
    }

    public function addWatermarkTextMask(Watermark $watermark)
    {
        $watermarkImage = new \Imagick();
        $mask = new \Imagick();
        $draw = new \ImagickDraw();

        $width = $this->getWidth();
        $height = $this->getHeight();

        $watermarkImage->newImage($width, $height, new \ImagickPixel($watermark->getMaskColor()));
        $mask->newImage($width, $height, new \ImagickPixel('black'));

        $draw->setFont($watermark->getFont());
        $draw->setFontSize($watermark->getSize());
        $draw->setFillColor($watermark->getColor());

        $draw->setGravity($watermark->getPosition());

        $watermarkImage->annotateImage($draw, 10, 12, 0, $watermark->getText());

        $draw->setFillColor('white');
        $mask->annotateImage($draw, 11, 13, 0, $watermark->getText());
        $mask->annotateImage($draw, 10, 12, 0, $watermark->getText());
        $draw->setFillColor('black');
        $mask->annotateImage($draw, 9, 11, 0, $watermark->getText());

        $mask->setImageMatte(false);

        $watermarkImage->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        return $this->getImage()->compositeImage($watermarkImage, \Imagick::COMPOSITE_DISSOLVE, 0, 0);
    }

    public function addWatermarkTextMosaic(Watermark $watermark)
    {
        $watermarkImage = new \Imagick();

        $draw = new \ImagickDraw();
        $watermarkImage->newImage(140, 80, new \ImagickPixel('none'));

        $draw->setFont($watermark->getFont());
        $draw->setFontSize($watermark->getSize());
        $draw->setFillColor($watermark->getColor());
        $draw->setFillOpacity($watermark->getOpacity());

        $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);

        $watermarkImage->annotateImage($draw, 10, 10, 0, $watermark->getText());

        $draw->setGravity(\Imagick::GRAVITY_SOUTHEAST);

        $watermarkImage->annotateImage($draw, 5, 15, 0, $watermark->getText());

        $width =  $this->getImage()->getImageWidth();
        $height = $this->getImage()->getImageHeight();

        for ($w = 0; $w < $width; $w += 140) {
            for ($h = 0; $h < $height; $h += 80) {
                $this->getImage()->compositeImage($watermarkImage, \Imagick::COMPOSITE_OVER, $w, $h);
            }
        }
    }

    public function clear()
    {
        return $this->getImage()->stripImage();
    }

    public function optimize($quality = 80, $compression = null)
    {
        if (null === $compression) {
            $compression = \Imagick::COMPRESSION_UNDEFINED;
        }
        $this->getImage()->setImageCompression($compression);
        $this->getImage()->setImageCompressionQuality($quality);
    }

    public function write($file)
    {
        $this->getImage()->writeImage($file);
    }
}
