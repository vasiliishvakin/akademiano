<?php

namespace Akademiano\User;

interface CustodianIncludeInterface
{
    public function setCustodian(AuthInterface $custodian);

    /**
     * @return AuthInterface
     */
    public function getCustodian();
}
