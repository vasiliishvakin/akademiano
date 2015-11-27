<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "imageProcessor" => function ($c) {
            $config = $c->getConfig("Image", []);
            $imp = new \Image\Model\Processor();
            $imp->setConfig($config);
            return $imp;
        },

];