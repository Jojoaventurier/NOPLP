<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Person;
use App\Entity\SongReview;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SongImportController extends AbstractController
{
    
    #[Route('/import-songs', name: 'import_songs', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('excel_file');

        if (!$file) {
            $this->addFlash('error', 'Aucun fichier fourni');
            return $this->redirectToRoute('app_song');
        }

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $rowCount = 0;
        $lastArtistName = null;

        foreach ($rows as $index => $row) {
            if ($index === 1) continue; // Ignore l'en-tête

            $artistName = $row['A'] ?? null;
            $title = $row['B'] ?? null;

            if (!$artistName && $lastArtistName) {
                $artistName = $lastArtistName;
            }

            if (!$artistName || !$title) continue;

            // Nettoyage du nom
            $artistName = trim($artistName);
            $lastArtistName = $artistName; // Stocke pour la prochaine ligne
            $existingPersons = [];

            $downloaded = $row['C'] ?? null;
            $hasLyrics = $row['D'] ?? null;
            $listened = $row['E'] ?? null;
            $userKnowledgeRaw = $row['F'] ?? null;
            $crossValue = $row['G'] ?? null;
            $playInfo = strtoupper(trim($row['H'] ?? ''));
            $reviewDate1 = $row['I'] ?? null;
            $reviewDate2 = $row['J'] ?? null;

            // Recherche ou création de l'artiste
            $artistName = trim($artistName); // nettoyage espaces
            if (isset($existingPersons[$artistName])) {
                $person = $existingPersons[$artistName];
            } else {
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $artistName]);
                if (!$person) {
                    $person = new Person();
                    $person->setName($artistName);
                    $person->setCategory('Femme');
                    $em->persist($person);
                }
                $existingPersons[$artistName] = $person;
            }

            // Recherche ou création de la chanson
            $song = $em->getRepository(Song::class)->findOneBy(['title' => $title]);
            if (!$song) {
                $song = new Song();
                $song->setTitle($title);
            }

            // Association de l'artiste à la chanson
            if (!$song->getPerson()->contains($person)) {
                $song->addPerson($person);
            }

            if (!empty($downloaded)) {
                $song->setIsDownloaded(true);
            }
            if (!empty($hasLyrics)) {
                $song->setHasLyrics(true);
            }
            if (!empty($listened)) {
                $song->setIsListened(true);
            }

            if (!empty($crossValue)) {
                $song->setUserSongKnowledge('by_heart');
            } elseif ($userKnowledgeRaw) {
                $mappedKnowledge = $this->mapKnowledge($userKnowledgeRaw);
                if ($mappedKnowledge) {
                    $song->setUserSongKnowledge($mappedKnowledge);
                }
            }

            $normalPlayCount = substr_count($playInfo, 'T');
            $noplpCount = substr_count($playInfo, 'C');
            $song->setNormalPlayCount($normalPlayCount);
            $song->setNoplpCount($noplpCount);

            $em->persist($song);

            // Création d'un SongReview si date valide
            if ($this->isValidDate($reviewDate1)) {
                $review = new SongReview();
                $review->setSong($song);
                $review->setReviewedAt(new \DateTimeImmutable($reviewDate1));
                $em->persist($review);
            } elseif ($this->isValidDate($reviewDate2)) {
                $review = new SongReview();
                $review->setSong($song);
                $review->setReviewedAt(new \DateTimeImmutable($reviewDate2));
                $em->persist($review);
            }

            $rowCount++;
        }

        $em->flush();

        $this->addFlash('success', "$rowCount chansons importées avec succès.");
        return $this->redirectToRoute('app_song');
    }
    
    private function mapKnowledge(?string $valeur): ?string
    {
        return match (strtolower(trim($valeur))) {
            'inconnue' => 'unknown',
            'un peu' => 'little',
            'bien' => 'well',
            'par cœur' => 'by_heart',
            default => null,
        };
    }
    
    private function isValidDate($value): bool
    {
        if (!$value) return false;
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}


