<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Attach\Model;


use DeltaPhp\Operator\Entity\NamedEntityInterface;

class ImageFileEntity extends FileEntity implements NamedEntityInterface
{
    public function isMain()
    {
        return true;

    }

}
