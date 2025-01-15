<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Frontend\FrontendUrlGenerator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class JoinConfirmationSender
{
    public function __construct(
        private MailerInterface $mailer,
        private FrontendUrlGenerator $urlGenerator,
        private Environment $twig,
    ) {
    }

    /**
     *
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(Email $email, Token $token): void
    {
        $message = (new MimeEmail())
            ->to($email->value())
            ->subject('Join Confirmation')
            ->html($this->twig->render('auth/join/confirm.html.twig', [
                'url' => $this->urlGenerator->generate('join/confirm', ['token' => $token->value()]),
            ]), 'text/html');

        $this->mailer->send($message);
    }
}
