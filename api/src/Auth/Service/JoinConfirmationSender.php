<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as MimeEmail;

final readonly class JoinConfirmationSender
{
    /** @param array{name: string, email: string} $from */
    public function __construct(
        private MailerInterface $mailer,
        private array $from,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, Token $token): void
    {
        $path = '/join/confirm?' . http_build_query(['token' => $token->value()]);

        $fromAddress = new Address(
            address: $this->from['email'],
            name: $this->from['name']
        );

        $emailInstance = (new MimeEmail())
            ->from($fromAddress)
            ->to($email->value())
            ->subject('Join Confirmation')
            ->text($path);

        $this->mailer->send($emailInstance);
    }
}
