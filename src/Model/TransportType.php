<?php


namespace Akademiano\Messages\Model;


use Akademiano\Utils\Object\IntegerEnum;

class TransportType extends IntegerEnum
{
    const __default = 1;
    const EMAIL = 1;
    const WEB = 2;
}
