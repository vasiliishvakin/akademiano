<?php

namespace Akademiano\Core;

use Akademiano\Config\Config;
use Akademiano\Core\View\TwigView;
use Akademiano\SimplaView\ViewInterface;

class ViewFactory
{

    /**
     * @param $adapterName
     * @param Config $config
     * @return ViewInterface
     */
    public static function getView($adapterName, Config $config = null)
    {
        $adapterName = strtolower($adapterName);
        switch($adapterName) {
            case 'twig' :
                $view = new TwigView();
                $view->setConfig($config);
                return $view;
                break;
            default:
                throw new \InvalidArgumentException("View adapter $adapterName not defined");
        }
    }
}
