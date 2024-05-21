<?php

namespace Tests\Unit\Service;

use App\Service\FileService;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class FileServiceTest.
 *
 * @covers \App\Service\FileService
 */
final class FileServiceTest extends TestCase
{
    private FileService $fileService;

    private EntityManagerInterface|MockObject $entityManager;

    private ParameterBagInterface|MockObject $parameterBag;

    private Connection|MockObject $connection;

    private string $upload_directory;

    private Filesystem|MockObject $filesystem;

    private SluggerInterface|MockObject $slugger;

    private UserPasswordHasherInterface|MockObject $encoder;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
        $this->connection = $this->createMock(Connection::class);
        $this->upload_directory = '42';
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->encoder = $this->createMock(UserPasswordHasherInterface::class);
        $this->fileService = new FileService($this->entityManager, $this->parameterBag, $this->connection, $this->upload_directory, $this->filesystem, $this->slugger, $this->encoder);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->fileService);
        unset($this->entityManager);
        unset($this->parameterBag);
        unset($this->connection);
        unset($this->upload_directory);
        unset($this->filesystem);
        unset($this->slugger);
        unset($this->encoder);
    }
}
