<?php


namespace Akademiano\Messages\Model;


use Akademiano\Utils\Object\IntegerEnum;

class Status extends IntegerEnum
{
    const __default = 0;

    const STATUS_NEW = 0;
    const STATUS_DONE = -1;
    const STATUS_DO = 1;
    const STATUS_ERROR = -2;
}
