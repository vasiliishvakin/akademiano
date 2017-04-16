<?php

namespace Akademiano\HttpWarp\Parts;

use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Request;

trait RequestIncludeTrait
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = new Request();
            if ($this instanceof EnvironmentIncludeInterface) {
                $this->request->setEnvironment($this->getEnvironment());
            }
        }
        return $this->request;
    }
}
