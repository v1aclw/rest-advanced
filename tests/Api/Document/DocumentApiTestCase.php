<?php

declare(strict_types=1);

namespace App\Tests\Api\Document;

use App\Tests\Api\ApiTestCase;

class DocumentApiTestCase extends ApiTestCase
{
    public static function asserDocumentData(string $expectedStatus, array $expectedPayload, array $actual): void
    {
        self::assertArrayHasKey('id', $actual);
        self::assertArrayHasKey('status', $actual);
        self::assertEquals($expectedStatus, $actual['status']);
        self::assertArrayHasKey('payload', $actual);
        self::assertEquals($expectedPayload, $actual['payload']);
        self::assertArrayHasKey('createAt', $actual);
        self::assertArrayHasKey('modifyAt', $actual);
    }
}
