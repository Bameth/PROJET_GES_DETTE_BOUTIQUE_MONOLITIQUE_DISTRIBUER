<?php

namespace App\Repository;

use App\Entity\Dette;
use App\Enum\StatusDettes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dette>
 */
class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    //    /**
    //     * @return Dette[] Returns an array of Dette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Dette
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findDetteByClientAndStatus(int $clientId, ?string $status = null): array
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->andWhere('c.id = :clientId')
            ->setParameter('clientId', $clientId);

        if ($status === StatusDettes::PAYEE->value) {
            $queryBuilder->andWhere('d.montant = d.montantVerser');
        } elseif ($status === StatusDettes::IMPAYE->value) {
            $queryBuilder->andWhere('d.montant > d.montantVerser');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
