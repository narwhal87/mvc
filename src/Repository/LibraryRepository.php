<?php

namespace App\Repository;

use App\Entity\Library;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Library>
 */
class LibraryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Library::class);
    }

    public function getBookDetails($isbn): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM library AS l
            WHERE l.isbn = :isbn
        ';

        $resultSet = $conn->executeQuery($sql, ['isbn' => $isbn]);

        return $resultSet->fetchAllAssociative();
    }
}
