<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const MASTER_ID = '4cf26a17-29a9-4fd0-b015-40cafcd5a034';
    public const SLAVE_ID = '2342c48e-dec4-49ad-866b-01375fe1f0ca';

    public const DATA = [
        self::MASTER_ID => [
            'login' => 'master',
        ],
        self::SLAVE_ID => [
            'login' => 'slave',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $id => $data) {
            $manager->persist(
                $user = new User($id, $data['login'])
            );
            $this->addReference($id, $user);
        }

        $manager->flush();
    }
}
