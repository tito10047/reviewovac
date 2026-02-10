<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

	public function findRandomReview():?Review {
        /** @var Review|null $review */
        $review = $this->createQueryBuilder("r")
            ->orderBy("RAND()")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $review;

	}
}
