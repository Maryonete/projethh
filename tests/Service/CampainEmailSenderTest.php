<?php

namespace Tests\Unit\Service;

use App\Repository\AssociationRepository;
use App\Service\CampainEmailSender;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CampainEmailSenderTest.
 *
 * @covers \App\Service\CampainEmailSender
 */
final class CampainEmailSenderTest extends TestCase
{
    private CampainEmailSender $campainEmailSender;

    private MailerInterface|MockObject $mailer;

    private AssociationRepository|MockObject $assoRepo;

    private EntityManagerInterface|MockObject $entityManager;

    private UrlGeneratorInterface|MockObject $urlGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mailer = $this->createMock(MailerInterface::class);
        $this->assoRepo = $this->createMock(AssociationRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->campainEmailSender = new CampainEmailSender($this->mailer, $this->assoRepo, $this->entityManager, $this->urlGenerator);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->campainEmailSender);
        unset($this->mailer);
        unset($this->assoRepo);
        unset($this->entityManager);
        unset($this->urlGenerator);
    }
}
