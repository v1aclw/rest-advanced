<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Token\TokenStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends WebTestCase
{
    protected static null|EntityManagerInterface $entityManager = null;
    protected static null|KernelBrowser $client = null;
    protected static null|TokenStorage $tokenStorage = null;

    public function setUp(): void
    {
        self::$client = static::createClient();
        self::$entityManager = self::getContainer()->get(EntityManagerInterface::class);
        self::$entityManager->getConnection()->setAutoCommit(false);
        self::$entityManager->getConnection()->beginTransaction();
        self::$tokenStorage = self::getContainer()->get(TokenStorage::class);
    }

    public function tearDown(): void
    {
        if (self::$entityManager->getConnection()->isTransactionActive()) {
            self::$entityManager->getConnection()->rollBack();
            self::$entityManager->close();
            self::$entityManager = null;
        }

        parent::tearDown();
    }

    protected static function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        null|array $jsonData = null
    ): Response {
        self::$client->request(
            $method,
            $uri,
            $parameters,
            [],
            array_merge(['CONTENT_TYPE' => 'application/json'], $headers),
            $jsonData ? json_encode($jsonData, JSON_THROW_ON_ERROR) : null
        );

        return self::$client->getResponse();
    }
}
