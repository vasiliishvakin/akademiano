<?php


namespace Akademiano\Sites\Site;


class DataStorage extends FlowDirectory
{
    const GLOBAL_DIR = "data";
    const INTERNAL_DIR = "data";

    /**
     * DataStore constructor.
     */
    public function __construct($internalPath, $globalPath)
    {
        parent::__construct($internalPath, $globalPath);
    }
}
