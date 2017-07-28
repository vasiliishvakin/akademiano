<?php

return [
    "custodianRequestDataTool" => function($c) {
        $rdt = new \Akademiano\UserEO\Model\Request\HttpSessionDataTool();
        $rdt->setSession($c["sessions"]);
        return $rdt;
    },

    'custodian' => function ($c) {
        $custodian = new \Akademiano\UserEO\Custodian();
        $custodian->setOperator($c["operator"]);
        $custodian->setRdt($c["custodianRequestDataTool"]);
        return $custodian;
    },

    'usersApi' => function($c) {
        $operator = $c["operator"];
        $api = new \Akademiano\UserEO\Api\v1\UsersApi($operator);
        if ($api instanceof \Akademiano\Acl\AccessCheckIncludeInterface) {
            $api->setAclManager($c["aclManager"]);
        }

        if ($api instanceof \Akademiano\User\CustodianIncludeInterface) {
            $api->setCustodian($c["custodian"]);
        }
        return $api;
    }
];
