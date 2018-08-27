<?php

return [
    'provider' => 'sqs',
    'default_connection' => null,
    'default_command' => 'swarrot.command.base',
    'logger' => 'logger',
    'connections' => [
        'name' => [
            'host' => '127.0.0.1',
            'port' => 5672,
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
            'ssl' => false,
            'region' => 'eu-west-1',
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
            'connection' => null,
            'queue' => null,
            'extras' => [],
            'middleware_stack' => [
                [
                    'configurator' => null,
                    'extras' => [
                        'foo' => 'bar',
                        'baz' => 'bar',
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
    'processors_stack' => [
    ],
];
