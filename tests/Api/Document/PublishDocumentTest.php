<?php

declare(strict_types=1);

namespace App\Tests\Api\Document;

use App\DataFixtures\DocumentFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PublishDocumentTest extends DocumentApiTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAction(string $id, int $statusCode, array $expectedPayload, null|string $token = null, null|string $userId = null): void
    {
        $headers = [];
        if (null !== $token) {
            $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        }
        if (null !== $token && null !== $userId) {
            self::$tokenStorage->set($token, $userId);
        }
        $response = self::request('POST', sprintf('/api/v1/document/%s/publish', $id), headers: $headers);

        self::assertEquals($statusCode, $response->getStatusCode());

        if (Response::HTTP_OK === $statusCode) {
            self::assertJson($response->getContent());
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertArrayHasKey('document', $responseData);
            self::asserDocumentData(Document::STATUS_PUBLISHED, $expectedPayload, $responseData['document']);
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                $documentId = DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_OK,
                DocumentFixtures::DATA[$documentId]['payload'],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_UNAUTHORIZED,
                [],
                (string) Uuid::v4()
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_FORBIDDEN,
                [],
                (string) Uuid::v4(),
                UserFixtures::SLAVE_ID
            ],
            [
                (string) Uuid::v4(),
                Response::HTTP_NOT_FOUND,
                [],
                (string) Uuid::v4(),
                UserFixtures::SLAVE_ID
            ]
        ];
    }
}
