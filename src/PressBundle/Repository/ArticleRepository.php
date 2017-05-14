<?php

namespace PressBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository {
    
    public function getArticlesFromUser($sortParameters, $userId) {
        $query = $this->createQueryBuilder("a")
            ->leftJoin("a.owner", "o")
            ->where("o.id = :userId")
            ->andWhere("a.archived = 0")
            ->orderBy("a." . $sortParameters["sortBy"], $sortParameters["sortDirection"])
            ->setParameter("userId", $userId)
            ->getQuery();

        return $query->getArrayResult();
    }
    
    public function getArticlesFromTag($tagId, $sortParameters, $userId) {
        $query = $this->createQueryBuilder("a")
            ->leftJoin("a.owner", "o")
            ->leftJoin("a.tags", "t")
            ->where("o.id = :userId")
            ->andWhere("t.id = :tagId")
            ->andWhere("a.archived = 0")
            ->orderBy("a." . $sortParameters["sortBy"], $sortParameters["sortDirection"])
            ->setParameter("userId", $userId)
            ->setParameter("tagId", $tagId)
            ->getQuery();

        return $query->getArrayResult();
    }
    
}
