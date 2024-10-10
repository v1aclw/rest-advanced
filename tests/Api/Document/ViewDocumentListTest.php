<?php

declare(strict_types=1);

namespace App\Tests\Api\Document;

use App\DataFixtures\DocumentFixtures;
use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ViewDocumentListTest extends DocumentApiTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAction(int $statusCode, array $expectedIds, null|string $token = null, null|string $userId = null): void
    {
        $headers = [];
        if (null !== $token) {
            $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        }
        if (null !== $token && null !== $userId) {
            self::$tokenStorage->set($token, $userId);
        }
        $response = self::request('GET', '/api/v1/document', headers: $headers);

        self::assertEquals($statusCode, $response->getStatusCode());

        if (Response::HTTP_OK === $statusCode) {
            self::assertJson($response->getContent());
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertArrayHasKey('document', $responseData);
            self::assertEquals($expectedIds, array_column($responseData['document'], 'id'));
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                Response::HTTP_OK,
                [DocumentFixtures::SLAVE_PUBLISHED_ID, DocumentFixtures::MASTER_PUBLISHED_ID, DocumentFixtures::MASTER_DRAFT_ID],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                Response::HTTP_OK,
                [DocumentFixtures::SLAVE_PUBLISHED_ID, DocumentFixtures::MASTER_PUBLISHED_ID],
            ],
        ];
    }
}
