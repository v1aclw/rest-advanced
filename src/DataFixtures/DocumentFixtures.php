<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\User;
use App\Json\JsonPatch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    public const MASTER_DRAFT_ID = 'c5532f63-df4f-4c29-aebe-6dc021680e37';
    public const MASTER_PUBLISHED_ID = '16b3390c-db9f-4a68-bd30-c4c0f25f1b14';
    public const SLAVE_DRAFT_ID = 'e346b4d1-f7a6-40d0-8324-26d42e137c88';
    public const SLAVE_PUBLISHED_ID = '0c557607-2232-4149-8d8c-0aa5b4a08076';

    public function __construct(private JsonPatch $jsonPatch)
    {
    }

    public const DATA = [
        self::MASTER_DRAFT_ID => [
            'status' => Document::STATUS_DRAFT,
            'user_id' => UserFixtures::MASTER_ID,
            'payload' => [
                'actor' => 'The fox', 
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
        ],
        self::MASTER_PUBLISHED_ID => [
            'status' => Document::STATUS_PUBLISHED,
            'user_id' => UserFixtures::MASTER_ID,
            'payload' => [
                'actor' => 'The fox', 
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
        ],
        self::SLAVE_DRAFT_ID => [
            'status' => Document::STATUS_DRAFT,
            'user_id' => UserFixtures::SLAVE_ID,
            'payload' => [],
        ],
        self::SLAVE_PUBLISHED_ID => [
            'status' => Document::STATUS_PUBLISHED,
            'user_id' => UserFixtures::SLAVE_ID,
            'payload' => [],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $id => $data) {
            $manager->persist(
                $document = new Document($id, $this->getReference($data['user_id'], User::class))
            );
            $document->editPayload($this->jsonPatch, $data['payload']);
            if (Document::STATUS_PUBLISHED === $data['status']) {
                $document->publish();
            }
            $this->addReference($id, $document);
            sleep(1);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
