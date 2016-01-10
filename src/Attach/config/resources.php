<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'fileManager' => function ($c) {
        $fm = new \Attach\Model\FileManager();
        $config = $c->getConfig();
        $fm->setConfig($config);
        $sm = $c["sequenceManager"];
        $fm->setSequenceManager($sm);
        $uuidFactory = $c["uuidFactory"];
        $fm->setUuidFactory($uuidFactory);
        return $fm;
    },
];
