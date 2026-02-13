<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /** Returns a random review entity. */
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
     * Returns IDs of all unprocessed reviews.
     */
    public function findUnprocessedIdsQB(): QueryBuilder
    {
        return $array = $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.processed = false');
    }
}
