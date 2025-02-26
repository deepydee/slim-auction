<?php

declare(strict_types=1);

namespace App\Frontend;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FrontendUrlTwigExtension extends AbstractExtension
{
    public function __construct(private readonly FrontendUrlGenerator $urlGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('frontend_url', [$this, 'url']),
        ];
    }

    /**
     * @param  array<string, int|string>  $params
     */
    public function url(string $path, array $params = []): string
    {
        return $this->urlGenerator->generate($path, $params);
    }
}
