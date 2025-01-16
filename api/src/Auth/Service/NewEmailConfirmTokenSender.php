<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class NewEmailConfirmTokenSender
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function send(Email $email, Token $token): void
    {
        $message = (new MimeEmail())
            ->to($email->value())
            ->subject('New Email Confirmation')
            ->html($this->twig->render('auth/email/confirm.html.twig', ['token' => $token]), 'text/html');


        $this->mailer->send($message);
    }
}
