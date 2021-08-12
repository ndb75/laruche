<?php

namespace App\Repository;

use App\Entity\Receiver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Receiver|null find($id, $lockMode = null, $lockVersion = null)
 * @method Receiver|null findOneBy(array $criteria, array $orderBy = null)
 * @method Receiver[]    findAll()
 * @method Receiver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receiver::class);
    }

    public function insertOrUpdate($firstName, $lastName, $uuid)
    {
        $this->getEntityManager()
            ->getConnection()
            ->prepare('
                INSERT INTO receiver (firstname, lastname, uuid)
                VALUES (:firstname, :lastname, :uuid)
                    ON DUPLICATE KEY UPDATE
                    firstname = VALUES(firstname),
                    lastname = VALUES(lastname),
                    uuid = VALUES(uuid)
                    ')
            ->execute([
                ':firstname'    => $firstName,
                ':lastname'     => $lastName,
                ':uuid'         => $uuid
            ]);
        return $this->getEntityManager()->getConnection()->lastInsertId();
    }

    // /**
    //  * @return Receiver[] Returns an array of Receiver objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Receiver
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
