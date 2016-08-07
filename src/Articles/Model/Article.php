<?php

namespace Articles\Model;

use Attach\Model\Parts\ImagesTrait;
use DeltaPhp\Operator\Entity\ContentEntity;
use DeltaPhp\Operator\Entity\ContentEntityInterface;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;

/**
 * Class Article
 * @package Articles
 */

class Article extends ContentEntity implements ArticleInterface, ContentEntityInterface, EntityInterface, DelegatingInterface
{

    use DelegatingTrait;
    use ImagesTrait;

    /**
     * @return mixed
     */
    public function getCategories()
    {
        throw new \LogicException("");
    }
}
