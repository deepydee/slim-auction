<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => static function (ContainerInterface $container): TranslatorInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{lang: string, resources: list<list<string>>} $config
         */
        $config = $container->get('config')['translator'];

        return new Translator($config['lang']);
    },

    'config' => [
        'translator' => [
            'lang' => 'en',
        ],
    ],
];
