<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 27.10.2015
 * Time: 15:40
 */

namespace DeltaTwigExt;


class AssetExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'delta_asset';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'asset_css',
                array($this, 'assetCss'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function assetCss()
    {

    }

}