<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findRandomReview(): ?Review
    {
        /** @var Review|null $review */
        $review = $this->createQueryBuilder('r')
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $review;
    }

    /**
     * @return Uuid[]
     */
    public function findUnprocessedIds(): array
    {
        /** @var array{id:Uuid} $array */
        $array = $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.processed = false')
            ->getQuery()
            ->getArrayResult();

        return array_column($array, 'id');
    }
}
