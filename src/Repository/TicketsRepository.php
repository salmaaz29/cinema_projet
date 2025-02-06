<?php

namespace App\Repository;

use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tickets>
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    //    /**
    //     * @return Tickets[] Returns an array of Tickets objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tickets
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
     // Méthode pour récupérer les films les plus demandés
     public function findMostRequestedFilms()
     {
         return $this->createQueryBuilder('t') // 't' est l'alias de Tickets
             ->select('f.titre AS titre', 'COUNT(t.id_tickets) AS ticket_count') // Sélectionner le titre du film et compter les tickets
             ->join('t.seance', 's') // Joindre avec l'entité Seance
             ->join('s.film', 'f') // Joindre avec l'entité Film
             ->groupBy('f.id_film') // Grouper par ID du film
             ->orderBy('ticket_count', 'DESC') // Trier par nombre de tickets vendus
             ->setMaxResults(5) // Limiter à 5 films les plus demandés
             ->getQuery()
             ->getResult();
     }
}
