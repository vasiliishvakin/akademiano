<?php

return [
    \Akademiano\Messages\Api\v1\MessagesApi::API_ID => function (\Akademiano\DI\Container $c) {
        return new \Akademiano\Messages\Api\v1\MessagesApi($c["operator"]);
    },
    "mailerTransport" => function (\Akademiano\DI\Container $c) {
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        return (new Swift_SmtpTransport(
            $config->getOrThrow(['email', 'smtp', 'host']),
            $config->get(['email', 'smtp', 'port'], '465')
        ))
            ->setUsername($config->getOrThrow(['email', 'smtp', 'username']))
            ->setPassword($config->getOrThrow(['email', 'smtp', 'password']))
            ->setEncryption($config->get(['email', 'smtp', 'encryption'], "ssl"))
            ->setAuthMode($config->get(['email', 'smtp', 'authMode'], "login"));
    },
    "mailer" => function (\Akademiano\DI\Container $c) {
        return new Swift_Mailer($c["mailerTransport"]);
    },
    \Akademiano\Messages\Api\v1\SendEmailsApi::API_ID => function (\Akademiano\DI\Container $c) {
        $api = new \Akademiano\Messages\Api\v1\SendEmailsApi($c["operator"]);
        $api->setMessagesApi($c[\Akademiano\Messages\Api\v1\MessagesApi::API_ID]);
        return $api;
    },
];
