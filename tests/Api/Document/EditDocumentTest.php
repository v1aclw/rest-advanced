<?php

declare(strict_types=1);

namespace App\Tests\Api\Document;

use App\DataFixtures\DocumentFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class EditDocumentTest extends DocumentApiTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAction(string $id, int $statusCode, array $data, array $expectedPayload = [], null|string $token = null, null|string $userId = null): void
    {
        $headers = [];
        if (null !== $token) {
            $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        }
        if (null !== $token && null !== $userId) {
            self::$tokenStorage->set($token, $userId);
        }
        $response = self::request('PATCH', sprintf('/api/v1/document/%s', $id), headers: $headers, jsonData: $data);

        self::assertEquals($statusCode, $response->getStatusCode());

        if (Response::HTTP_OK === $statusCode) {
            self::assertJson($response->getContent());
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertArrayHasKey('document', $responseData);
            self::asserDocumentData(Document::STATUS_DRAFT, $expectedPayload, $responseData['document']);
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_UNAUTHORIZED,
                [],
                [],
                (string) Uuid::v4(),
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_BAD_REQUEST,
                [],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_BAD_REQUEST,
                ['document' => []],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_BAD_REQUEST,
                ['document' => ['payload' => 'test']],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_BAD_REQUEST,
                ['document' => ['payload' => [1, 2, 3]]],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                (string) Uuid::v4(),
                Response::HTTP_NOT_FOUND,
                ['document' => ['payload' => ['foo' => 'bar']]],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_FORBIDDEN,
                ['document' => ['payload' => ['foo' => 'bar']]],
                [],
                (string) Uuid::v4(),
                UserFixtures::SLAVE_ID
            ],
            [
                DocumentFixtures::MASTER_PUBLISHED_ID,
                Response::HTTP_BAD_REQUEST,
                ['document' => ['payload' => ['foo' => 'bar']]],
                [],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                $documentId = DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_OK,
                [
                    'document' => ['payload' => []]
                ],
                DocumentFixtures::DATA[$documentId]['payload'],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                $documentId = DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_OK,
                [
                    'document' => [
                        'payload' => [
                            'meta' => [
                                'type' => 'quick',
                                'color' => null
                            ],
                            'action' => [
                                [
                                    'action' => 'none',
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'actor' => 'The fox',
                    'meta' => [
                        'type' => 'quick',
                    ],
                    'action' => [
                        [
                            'action' => 'none',
                        ]
                    ]
                ],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ],
            [
                $documentId = DocumentFixtures::MASTER_DRAFT_ID,
                Response::HTTP_OK,
                [
                    'document' => [
                        'payload' => [
                            'actor' => ['primary' => 'The fox'],
                        ]
                    ]
                ],
                [
                    'actor' => ['primary' => 'The fox'],
                    'meta' => [
                        'type' => 'quick',
                        'color' => 'brown'
                    ],
                    'action' => [
                        [
                            'action' => 'jump over',
                            'actor' => 'lazy dog'
                        ]
                    ]
                ],
                (string) Uuid::v4(),
                UserFixtures::MASTER_ID
            ]
        ];
    }
}
