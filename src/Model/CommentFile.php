<?php


namespace Akademiano\Content\Comments\Model;


use Akademiano\Attach\Model\LinkedFile;

class CommentFile extends LinkedFile
{
    const ENTITY_CLASS = Comment::class;
}
