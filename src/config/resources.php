<?php

return [
    "imageProcessor" => function (\Akademiano\DI\Container $c) {
        /** @var \Akademiano\Config\Config $config */
        $config = $c['config'];
        $processor = new \PHPixie\Image($config->get(['content', 'files', 'image', 'processor', 'driver'], 'gd'));
        return $processor;
    },

];
