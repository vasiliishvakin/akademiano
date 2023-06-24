<?php
return [
    \Akademiano\UUID\UuidFactory::RESOURCE_ID => function (\Pimple\Container $c) {
        $f = new \Akademiano\UUID\UuidFactory();
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        $epoch = $config->get(["UUID", "complexShort", "epoch"], 1451317149374);
        $f->setEpoch($epoch);
        return $f;
    }
];
