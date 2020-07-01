<?php

use hiqdev\yii\compat\yii;

return [
    /// Middlewares
    \hiapi\Core\Http\Psr15\RequestHandler::class => [
        '__construct()' => [
            'quiet'         => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\QuietMiddleware::class),
            'content-type'  => yii::referenceTo(\Lcobucci\ContentNegotiation\ContentTypeMiddleware::class),
            'exception'     => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\ExceptionMiddleware::class),
            'blacklist'     => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\BlacklistMiddleware::class),
            'user-real-ip'  => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\UserRealIpMiddleware::class),
            'auth'          => yii::referenceTo(\hiapi\Core\Auth\AuthMiddleware::class),
            'cors'          => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\CorsMiddleware::class),
            'perform'       => yii::referenceTo('hiapi-endpoint-middleware'),
            new \Yiisoft\Arrays\Modifier\RemoveKeys(),
        ],
    ],

    'hiapi-endpoint-middleware' => [
        '__class' => \hiapi\Core\Http\Psr15\Middleware\RelayMiddleware::class,
        '__construct()' => [
            'resolve'       => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\ResolveEndpointMiddleware::class),
            'build-command' => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\CommandForEndpointMiddleware::class),
            'run'           => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\RunEndpointBusMiddleware::class),
            new \Yiisoft\Arrays\Modifier\RemoveKeys(),
        ],
    ],

    \Lcobucci\ContentNegotiation\ContentTypeMiddleware::class => [
        '__construct()' => [
            yii::referenceTo('content-types'),
            [
                'text/json'                 => yii::referenceTo(\hiapi\Core\Console\Formatter\Json::class),
                'application/json'          => yii::referenceTo(\hiapi\Core\Console\Formatter\Json::class),
                'application/x-json'        => yii::referenceTo(\hiapi\Core\Console\Formatter\Json::class),
                'application/vnd.api+json'  => yii::referenceTo(\hiapi\Core\Console\Formatter\JsonApi::class),
                'text/plain'                => yii::referenceTo(\hiapi\Core\Console\Formatter\Text::class),
                'text/php'                  => yii::referenceTo(\hiapi\Core\Console\Formatter\Php::class),
                'text/x-php'                => yii::referenceTo(\hiapi\Core\Console\Formatter\Php::class),
                'application/php'           => yii::referenceTo(\hiapi\Core\Console\Formatter\Php::class),
            ],
            new \Laminas\Diactoros\StreamFactory(),
        ],
    ],
    'content-types' => [
        '__class' => \Middlewares\ContentType::class,
        '__construct()' => [
            [
                'json' => [
                    'extension' => ['json'],
                    'mime-type' => ['application/json', 'application/x-json', 'text/json'],
                    'charset' => true,
                ],
                'jsonapi' => [
                    'extension' => ['jsonapi'],
                    'mime-type' => ['application/vnd.api+json'],
                    'charset' => true,
                ],
                'text' => [
                    'extension' => ['txt'],
                    'mime-type' => ['text/plain'],
                    'charset' => true,
                ],
                'php' => [
                    'extension' => ['php'],
                    'mime-type' => ['text/php', 'text/x-php', 'application/php'],
                    'charset' => true,
                ],
            ],
        ],
    ],

    \hiapi\Core\Http\Psr15\Middleware\UserRealIpMiddleware::class => [
        '__construct()' => [
            $params['hiapi.trustedRemoteNetworks'],
        ],
    ],

    \hiapi\Core\Http\Psr15\Middleware\BlacklistMiddleware::class => [
        '__construct()' => [
            $params['hiapi.BlacklistMiddleware.restriction'],
        ],
    ],

    \hiapi\Core\Auth\AuthMiddleware::class =>
        yii::referenceTo(\hiapi\Core\Auth\OAuth2Middleware::class),

    \hiapi\Core\Auth\OAuth2Middleware::class => [
        'userinfoUrl' => $params['hiapi.oauth2.userinfoUrl'],
    ],

    \hiapi\Core\Endpoint\EndpointRepository::class => [
        '__class' => \hiapi\Core\Endpoint\EndpointRepository::class,
        '__construct()' => [
            'endpoints' => [
                'OpenAPISchema' => \hiapi\Service\OpenApi\Endpoint\OpenAPISchema::class,
            ],
            new \Yiisoft\Arrays\Modifier\RemoveKeys(),
        ],
    ],
];
