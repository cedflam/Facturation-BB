<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }


    /**
     * Récupère l'ensemble des acomptes par mois sur l' année en cours
     *
     * @param $dateDebut
     * @param $dateFin
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAdvanceByPeriode($dateDebut, $dateFin): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT YEAR(created_at) AS "year", MONTH(created_at) AS "month", SUM(total_advance) AS "totalAdvances"
            FROM invoice 
            WHERE created_at 
            BETWEEN :dateDebut AND :dateFin 
            GROUP BY YEAR(created_at), MONTH(created_at)    
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        return $stmt->fetchAllAssociative();
    }

    /**
     * Récupère l'ensemble des factures finales éditée par mois sur l'année en cours
     *
     * @param $dateDebut
     * @param $dateFin
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotalRemainingFacturedByPeriode($dateDebut, $dateFin) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT YEAR(created_at) AS "year", MONTH(created_at) AS "month", SUM(remaining_capital) AS "total"
            FROM invoice 
            WHERE created_at 
            BETWEEN :dateDebut AND :dateFin 
            AND type_invoice = "finale"
            AND state = 1
            GROUP BY YEAR(created_at), MONTH(created_at)    
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        return $stmt->fetchAllAssociative();
    }

    /**
     * Total des capitaux restants dûs par momis sur l'année en cours
     *
     * @param $dateDebut
     * @param $dateFin
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotalRemainingByPeriode($dateDebut, $dateFin): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT YEAR(created_at) AS "year", MONTH(created_at) AS "month", SUM(remaining_capital) AS "total"
            FROM invoice 
            WHERE created_at 
            BETWEEN :dateDebut AND :dateFin 
            AND type_invoice = "acompte"
            AND state = 0
            GROUP BY YEAR(created_at), MONTH(created_at)    
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        return $stmt->fetchAllAssociative();
    }


    /*
    public function findOneBySomeField($value): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
