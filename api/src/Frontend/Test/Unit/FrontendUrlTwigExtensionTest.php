<?php

declare(strict_types=1);

namespace App\Frontend\Test\Unit;

use App\Frontend\FrontendUrlGenerator;
use App\Frontend\FrontendUrlTwigExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
#[CoversClass(FrontendUrlTwigExtension::class)]
final class FrontendUrlTwigExtensionTest extends TestCase
{
    /**
     * @throws SyntaxError
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Test]
    public function it_can_successfully_generate_url(): void
    {
        $urlGenerator = $this->createMock(FrontendUrlGenerator::class);
        $urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::equalTo('path'),
                self::equalTo(['a' => 1, 'b' => 2])
            )
            ->willReturn('http://test/path?a=1&b=2');

        $twig = new Environment(new ArrayLoader([
            'page.html.twig' => '<p>{{ frontend_url(\'path\', {\'a\': 1, \'b\': 2}) }}</p>',
        ]));

        $twig->addExtension(new FrontendUrlTwigExtension($urlGenerator));

        self::assertEquals('<p>http://test/path?a=1&amp;b=2</p>', $twig->render('page.html.twig'));
    }
}
