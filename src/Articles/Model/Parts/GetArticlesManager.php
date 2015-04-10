<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Model\Parts;

trait GetArticlesManager
{
    /**
     * @return \DeltaCore\Application
     */
    abstract function getApplication();

    /**
     * @return \Articles\Model\ArticlesManager
     */
    public function getArticlesManager()
    {
        $app = $this->getApplication();
        return $app["ArticlesManager"];
    }
} 