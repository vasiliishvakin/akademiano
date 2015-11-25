<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 25.11.15
 * Time: 1:27
 */

namespace Image\Model;


class Watermark
{
    protected $text = "Copyright";
    protected $font = "Courier";
    protected $size = 20;
    protected $color = "grey70";
    protected $maskColor = "grey30";
    protected $position = \Imagick::GRAVITY_SOUTHEAST;

}