<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Frontend\FrontendUrlGenerator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

final readonly class JoinConfirmationSender
{
    public function __construct(
        private MailerInterface $mailer,
        private FrontendUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, Token $token): void
    {
        $emailInstance = (new MimeEmail())
            ->to($email->value())
            ->subject('Join Confirmation')
            ->text($this->urlGenerator->generate(uri: 'join/confirm', params: ['token' => $token->value()]));

        $this->mailer->send($emailInstance);
    }
}
