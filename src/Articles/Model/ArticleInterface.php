<?php


namespace Articles\Model;


use DeltaPhp\Operator\Entity\ContentEntityInterface;

interface ArticleInterface extends ContentEntityInterface
{
    public function getCategories();

    public function getImages();

    public function getTitleImage();

    public function getOtherImages();
}