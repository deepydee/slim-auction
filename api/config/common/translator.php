<?php

use App\Http\Middleware\TranslatorLocale;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => DI\get(Translator::class),

    Translator::class => static function (ContainerInterface $container): Translator {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{lang: string, resources: list<list<string>>} $config
         */
        $config = $container->get('config')['translator'];

        $translator = new Translator($config['lang']);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addLoader('xlf', new XliffFileLoader());

        foreach ($config['resources'] as $resource) {
            /** @psalm-suppress InvalidScalarArgument  */
            $translator->addResource(...$resource);
        }

        return $translator;
    },

    TranslatorLocale::class => static function (ContainerInterface $container): TranslatorLocale {
        $translator = $container->get(Translator::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{allowed: list<string>} $config
         */
        $config = $container->get('config')['locales'];

        return new TranslatorLocale($translator, $config['allowed']);
    },

    'config' => [
        'translator' => [
            'lang' => 'en',
            'resources' => [
                [
                    'xlf',
                    __DIR__ . '/../../vendor/symfony/validator/Resources/translations/validators.ru.xlf',
                    'ru',
                    'validators',
                ],
                [
                    'php',
                    __DIR__ . '/../../translations/exceptions.ru.php',
                    'ru',
                    'exceptions',
                ],
            ],
        ],
        'locales' => [
            'allowed' => ['en', 'ru'],
        ],
    ],
];
