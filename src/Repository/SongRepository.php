<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    public function findAllWithDetails()
    {
        $songs = $this->createQueryBuilder('s')
            ->leftJoin('s.person', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();
    
        $this->addLatestReviewDates($songs);
        
        return $songs;
    }
    
    private function addLatestReviewDates(array $songs): void
    {
        if (empty($songs)) {
            return;
        }
    
        $songIds = array_map(fn($song) => $song->getId(), $songs);
        
        $reviewDates = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(r.song) as songId, MAX(r.reviewedAt) as latestDate')
            ->from('App\Entity\SongReview', 'r')
            ->where('r.song IN (:ids)')
            ->setParameter('ids', $songIds)
            ->groupBy('r.song')
            ->getQuery()
            ->getResult();
    
        $datesMap = [];
        foreach ($reviewDates as $result) {
            // Handle both array and object results
            $date = is_array($result) ? $result['latestDate'] : $result->getLatestDate();
            
            if ($date instanceof \DateTimeInterface) {
                $datesMap[is_array($result) ? $result['songId'] : $result->getSongId()] = $date;
            } elseif ($date !== null) {
                try {
                    $datesMap[is_array($result) ? $result['songId'] : $result->getSongId()] = new \DateTime($date);
                } catch (\Exception $e) {
                    // Handle invalid date strings if needed
                    continue;
                }
            }
        }
        
        foreach ($songs as $song) {
            $song->setLatestReviewDate($datesMap[$song->getId()] ?? null);
        }
    }

    //    /**
    //     * @return Song[] Returns an array of Song objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Song
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
