<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

use hiqdev\yii\compat\yii;

$app = [
    'id' => 'hiapi',
    'name' => 'HiAPI',
    'basePath' => dirname(__DIR__) . '/src',
    'viewPath' => '@hiapi/views',
];

$components = [
    (yii::is3() ? 'logger' : 'log') => [
        'targets' => [
            [
                '__class' => \yii\log\FileTarget::class,
                'logFile' => '@runtime/error.log',
                'levels' => [\Psr\Log\LogLevel::ERROR],
                'logVars' => [],
            ],
        ],
    ],
];

$singletons = array_merge(
    include __DIR__ . '/old-bus-request-handling.php',
    include __DIR__ . '/request-handling.php',
    [
        \yii\web\User::class => [
            'identityClass' => \hiapi\Core\Auth\UserIdentity::class,
            'enableSession' => false,
        ],

    /// Event
        \hiapi\event\EventStorageInterface::class => \hiapi\event\EventStorage::class,
        \League\Event\EmitterInterface::class => [
            '__class' => \hiapi\event\ConfigurableEmitter::class,
            'listeners' => array_filter([
                YII_ENV === 'dev'
                    ? ['event' => '*', 'listener' => \hiapi\event\listener\LogEventsListener::class]
                    : null,
            ]),
        ],

    /// Queue
        \PhpAmqpLib\Connection\AMQPStreamConnection::class => [
            '__class' => \PhpAmqpLib\Connection\AMQPLazyConnection::class,
            '__construct()' => [
                $params['amqp.host'],
                $params['amqp.port'],
                $params['amqp.user'],
                $params['amqp.password'],
            ],
        ],

    /// General
        \yii\di\Container::class => function ($container) {
            return $container;
        },
        \yii\mail\MailerInterface::class => function () {
            return \hiqdev\yii\compat\yii::getApp()->get('mailer');
        },
    ]
);

return yii::is3() ? array_merge([
    'aliases' => $aliases,
    'app' => $app,
], $components, $singletons) : array_merge([
    'bootstrap' => ['log'],
    'aliases' => $aliases,
    'components' => $components,
    'container' => [
        'singletons' => $singletons,
    ],
    'params' => $params,
    'vendorPath' => '@root/vendor',
    'runtimePath' => '@root/runtime',
], $app);
