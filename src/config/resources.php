<?php

return [
    "commentsApi" => function (\Pimple\Container $c) {
        $operator = $c["Operator"];
        $api = new \Akademiano\Content\Comments\Api\v1\CommentsApi($operator);

        if ($api instanceof \Akademiano\Acl\AccessCheckIncludeInterface) {
            $api->setAclManager($c["aclManager"]);
        }
        return $api;
    },
];
