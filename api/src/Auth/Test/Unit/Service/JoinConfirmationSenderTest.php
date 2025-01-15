<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\JoinConfirmationSender;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

/**
 * @internal
 */
#[CoversClass(JoinConfirmationSender::class)]
final class JoinConfirmationSenderTest extends TestCase
{
    /**
     * @throws Exception|TransportExceptionInterface
     */
    #[Test]
    public function join_confirmation_email_can_be_successfully_sent(): void
    {
        $frontendUrl = 'http://test';
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable());
        $body = $frontendUrl . '/join/confirm?token=' . $token->value();

        $mailer = $this->createMock(MailerInterface::class);

        $mailer->expects(self::once())->method('send')
            ->willReturnCallback(static function (MimeEmail $message) use ($to, $body): void {
                self::assertSame($to->value(), $message->getTo()[0]->getAddress());
                self::assertSame('Join Confirmation', $message->getSubject());
                self::assertSame($body, $message->getTextBody());
            });

        $sender = new JoinConfirmationSender($mailer, $frontendUrl);

        $sender->send($to, $token);
    }
}
