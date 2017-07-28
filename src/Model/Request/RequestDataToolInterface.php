<?php

namespace Akademiano\UserEO\Model\Request;

interface RequestDataToolInterface
{
    public function isAuthenticate();

    public function getCurrentUserId();
}
