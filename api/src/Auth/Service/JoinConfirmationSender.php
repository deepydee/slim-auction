<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

final readonly class JoinConfirmationSender
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, Token $token): void
    {
        $path = '/join/confirm?' . http_build_query(['token' => $token->value()]);

        $emailInstance = (new MimeEmail())
            ->to($email->value())
            ->subject('Join Confirmation')
            ->text($path);

        $this->mailer->send($emailInstance);
    }
}
