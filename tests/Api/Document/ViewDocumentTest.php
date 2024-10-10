<?php

declare(strict_types=1);

namespace App\Tests\Api\Document;

use App\DataFixtures\DocumentFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ViewDocumentTest extends DocumentApiTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAction(string $id, int $statusCode, string $expectedStatus, array $expectedPayload, null|string $token = null, null|string $userId = null): void
    {
        $headers = [];
        if (null !== $token) {
            $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        }
        if (null !== $token && null !== $userId) {
            self::$tokenStorage->set($token, $userId);
        }
        $response = self::request('GET', sprintf('/api/v1/document/%s', $id), headers: $headers);

        self::assertEquals($statusCode, $response->getStatusCode());

        if (Response::HTTP_OK === $statusCode) {
            self::assertJson($response->getContent());
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertArrayHasKey('document', $responseData);
            self::asserDocumentData($expectedStatus, $expectedPayload, $responseData['document']);
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                $documentId = DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_OK,
                Document::STATUS_DRAFT,
                DocumentFixtures::DATA[$documentId]['payload'],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_UNAUTHORIZED,
                Document::STATUS_DRAFT,
                [],
                (string) Uuid::v4()
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_FORBIDDEN,
                Document::STATUS_DRAFT,
                [],
                (string) Uuid::v4(),
                UserFixtures::SLAVE_ID
            ],
            [
                (string) Uuid::v4(),
                Response::HTTP_NOT_FOUND,
                Document::STATUS_DRAFT,
                [],
                (string) Uuid::v4(),
                UserFixtures::SLAVE_ID
            ],
            [
                $documentId = DocumentFixtures::MASTER_PUBLISHED_ID,
                Response::HTTP_OK,
                Document::STATUS_PUBLISHED,
                DocumentFixtures::DATA[$documentId]['payload']
            ],
        ];
    }
}
