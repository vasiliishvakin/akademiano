<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "imageProcessor" => function (\DeltaCore\Prototype\ConfigInterface $c) {
            $config = $c->getConfig("Image", []);
            $imp = new \Image\Model\Processor();
            $imp->setConfig($config);
            return $imp;
        },

];
