<?php
return [
    "uuidFactory" => function ($c) {
        $f = new \UUID\Model\UuidFactory();
        $epoch = $c->getConfig(["UUID", "complexShort", "epoch"], 1451317149374);
        $f->setEpoch($epoch);
        return $f;
    }
];
