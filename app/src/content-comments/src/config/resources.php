<?php

return [
    "commentsApi" => function (\Pimple\Container $c) {
        $operator = $c["Operator"];
        $api = new \Akademiano\Content\Comments\Api\v1\CommentsApi($operator);

        if ($api instanceof \Akademiano\Acl\AccessCheckIncludeInterface) {
            $api->setAclManager($c["aclManager"]);
        }
        $api->setFilesApi($c[\Akademiano\Content\Comments\Api\v1\CommentFilesApi::API_ID]);
        return $api;
    },
    \Akademiano\Content\Comments\Api\v1\CommentFilesApi::API_ID => function (\Pimple\Container $c) {
        $operator = $c["Operator"];
        $api = new \Akademiano\Content\Comments\Api\v1\CommentFilesApi($operator);

        if ($api instanceof \Akademiano\Acl\AccessCheckIncludeInterface) {
            $api->setAclManager($c["aclManager"]);
        }
        return $api;
    },
];
