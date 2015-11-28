<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 23.11.2015
 * Time: 14:38
 */

namespace Image\Model;


use DeltaCore\Parts\Configurable;
use Image\Model\Driver\AbstractDriver;
use Image\Model\Driver\Imagick;

/**
 * Class Image
 * @package Image\Model
 * @method read($file);
 * @method resize($width = null, $height = null);
 * @method crop($width = null, $height = null);
 * @method getWidth();
 * @method getHeight();
 * @method resizeToWidth($width)
 * @method resizeToHeight($height)
 * @method resizeAndCrop($width, $height)
 * @method addWatermarkText($text = "Copyright", $font = "Courier", $size = 20, $color = "grey70", $position = \Imagick::GRAVITY_SOUTHEAST)
 * @method addWatermarkTextMask($text = "Copyright", $font = "Courier", $size = 20, $color = "grey70", $position = \Imagick::GRAVITY_SOUTHEAST)
 * @method addWatermarkTextMosaic($text = "Copyright", $font = "Courier", $size = 20, $color = "grey70", $position = \Imagick::GRAVITY_SOUTHEAST)
 * @method clear()
 * @method optimize($quality = 60, $compression = null)
 */
class Image
{
    use Configurable;

    protected $image;

    protected $driver;

    protected $file;

    public function __construct($file = null)
    {
        if (null !== $file) {
            $this->setFile($file);
        }
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
     * @return string
     */
    public function getDriver()
    {
        if (is_null($this->driver)) {
            $this->driver = $this->getConfig("driver", "Imagick");
        }

        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return Imagick
     */
    public function getImage()
    {
        if (is_null($this->image)) {
            $driver = $this->getDriver();
            $driver = __NAMESPACE__ . "\\Driver\\" . $driver;
            /** @var AbstractDriver image */
            $this->image = new $driver();
            $this->image->setConfig($this->getConfig(lcfirst($driver), []));
            $this->image->setFile($this->getFile());
        }

        return $this->image;
    }

    function __call($name, $arguments)
    {
        $image = $this->getImage();
        if (!method_exists($image, $name)) {
            throw  new \LogicException("Method $name not exist");
        }

        return call_user_func_array([$image, $name], $arguments);
    }

    public function write($file)
    {
        $dir = dirname($file);
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0750, true)) {
                throw new \RuntimeException("Image write directory not exist and we couldn't create it.");
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException("Image write directory not writable.");
        }
        $this->getImage()->write($file);
    }
}
