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
                'verify_peer_name' => null,
                'allow_self_signed' => null,
                'cafile' => null,
                'local_cert' => null,
                'ciphers' => null,
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
            'middleware_stack' => [
                [
                    'configurator' => null,
                    'extras' => [
                        'foo' => 'bar',
                        'baz' => [
                            'bar',
                        ],
                    ],
                    'first_arg_class' => null,
                ],
            ],
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
    'enable_collector' => true,
];
