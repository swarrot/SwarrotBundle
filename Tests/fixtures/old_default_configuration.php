<?php

return [
    'provider' => 'pecl',
    'default_connection' => null,
    'default_command' => 'swarrot.command.base',
    'logger' => 'logger',
    'publisher_confirm_enable' => false,
    'publisher_confirm_timeout' => 0,
    'connections' => [
        'name' => [
            'host' => '127.0.0.1',
            'port' => 5672,
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
            'ssl' => false,
            'ssl_options' => [
                'verify_peer' => true,
                'cafile' => null,
                'local_cert' => null,
            ],
        ],
    ],
    'consumers' => [
        'name' => [
            'processor' => null,
            'command' => null,
            'command_aliases' => [],
            'connection' => null,
            'queue' => null,
            'extras' => [],
            'middleware_stack' => [],
        ],
    ],
    'messages_types' => [
        'name' => [
            'connection' => null,
            'exchange' => null,
            'routing_key' => null,
            'extras' => [],
        ],
    ],
    'processors_stack' => [
        'name' => null,
    ],
    'enable_collector' => true,
];
