<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function getListQuery(null|User $user): Query
    {
        $queryBuilder = $this
            ->createQueryBuilder('d')
            ->where('d.status = :statusPublished')
            ->setParameter('statusPublished', Document::STATUS_PUBLISHED);

        if (null !== $user) {
            $queryBuilder
                ->where('(d.status = :statusDraft AND d.user = :user) OR d.status = :statusPublished')
                ->setParameter('statusDraft', Document::STATUS_DRAFT)
                ->setParameter('user', $user);
        }

        return $queryBuilder
            ->orderBy('d.modifiedAt', 'desc')
            ->getQuery();
    }
}
