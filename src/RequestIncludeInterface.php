<?php


namespace Akademiano\HttpWarp;


interface RequestIncludeInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function setRequest(Request $request);

    /**
     * @return Request
     */
    public function getRequest();
}
