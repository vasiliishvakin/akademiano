<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 25.11.15
 * Time: 1:27
 */

namespace Image\Model;


use DeltaUtils\Parts\SetParams;

class Watermark
{
    use SetParams;

    const MODE_TEXT = 1;
    const MODE_TEXT_MASK = 2;
    const MODE_TEXT_MOSAIC = 3;
    const MODE_IMAGE = 4;
    const MODE_IMAGE_MOSAIC = 5;

    protected $text = "Copyright";
    protected $font = "Courier";
    protected $size = 20;
    protected $color = "grey70";
    protected $maskColor = "grey30";
    protected $position = \Imagick::GRAVITY_SOUTHEAST;
    protected $opacity = 0.4;
    protected $mode = self::MODE_TEXT;
    protected $width = 140;
    protected $height = 80;
    protected $file;

    /** @var  \Callable */
    protected $xFunction;
    /** @var  \Callable */
    protected $yFunction;

    public function __construct(array $params = null)
    {
        if (null !== $params) {
            $this->setParams($params);
        }
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    public function setFont($font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function getMaskColor()
    {
        return $this->maskColor;
    }

    public function setMaskColor($maskColor)
    {
        $this->maskColor = $maskColor;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return float
     */
    public function getOpacity()
    {
        return $this->opacity;
    }

    public function setOpacity($opacity)
    {
        $this->opacity = $opacity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return Callable
     */
    public function getXFunction()
    {
        if (null === $this->xFunction){
            $this->xFunction = function($width, $height, \Imagick $watermarkImage) {
                return $width - $watermarkImage->getImageWidth();
            };
        }
        return $this->xFunction;
    }

    /**
     * @param Callable $xFunction
     */
    public function setXFunction($xFunction)
    {
        $this->xFunction = $xFunction;
    }

    /**
     * @return Callable
     */
    public function getYFunction()
    {
        if (null === $this->yFunction){
            $this->yFunction = function($height, $width, \Imagick $watermarkImage) {
                return $height - $watermarkImage->getImageHeight();
            };
        }
        return $this->yFunction;
    }

    /**
     * @param Callable $yFunction
     */
    public function setYFunction($yFunction)
    {
        $this->yFunction = $yFunction;
    }

    public function getX($width, $height, $watermarkImage)
    {
        return call_user_func($this->getXFunction(), $width, $height, $watermarkImage);
    }
    public function getY($height, $width, $watermarkImage)
    {
        return call_user_func($this->getYFunction(), $height, $width, $watermarkImage);
    }

}
