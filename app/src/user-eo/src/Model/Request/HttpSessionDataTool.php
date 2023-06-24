<?php

namespace Akademiano\UserEO\Model\Request;

use Akademiano\HttpWarp\Session;

class HttpSessionDataTool implements RequestDataToolInterface
{
    const SESSION_VAR = "user_id";

    /** @var  Session */
    protected $session;

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    public function isAuthenticate()
    {
        return $this->getSession()->has(self::SESSION_VAR);
    }

    public function getCurrentUserId()
    {
        return $this->getSession()->get(self::SESSION_VAR);
    }

    public function setCurrentUserId($id)
    {
        $this->getSession()->set(self::SESSION_VAR, $id);
    }

    public function deleteCurrentUserId()
    {
        if ($this->isAuthenticate()) {
            $this->getSession()->delete(self::SESSION_VAR);
        }
    }
}
