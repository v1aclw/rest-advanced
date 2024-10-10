<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class LoginTest extends ApiTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAction(int $statusCode, array $body): void
    {
        $response = self::request('POST', '/api/v1/login', jsonData: $body);

        self::assertEquals($statusCode, $response->getStatusCode());
        self::assertJson($response->getContent());

        if (Response::HTTP_OK === $statusCode) {
            $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertArrayHasKey('user', $responseData);
            self::assertArrayHasKey('token', $responseData);
            self::assertArrayHasKey('until', $responseData);
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                Response::HTTP_OK,
                [
                    'login' => 'root',
                ],
            ],
            [
                Response::HTTP_BAD_REQUEST,
                [
                    'login' => ''
                ]
            ],
            [
                Response::HTTP_BAD_REQUEST,
                [
                    'login' => null
                ]
            ],
            [
                Response::HTTP_BAD_REQUEST,
                [
                    'login' => []
                ]
            ],
            [
                Response::HTTP_BAD_REQUEST,
                []
            ]
        ];
    }
}
