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
            if ($index === 1) continue; // Ignore header
    
            $artistName = $row['A'] ?? null;
            $title = $row['B'] ?? null;
            $downloaded = $row['C'] ?? null;
            $hasLyrics = $row['D'] ?? null;
            $listened = $row['E'] ?? null;
            $userKnowledgeRaw = $row['F'] ?? null;
            $crossValue = $row['G'] ?? null;
            $playInfo = strtoupper(trim($row['H'] ?? ''));
    
            // Gestion des cellules fusionnées : si vide, réutiliser le dernier nom d'artiste.
            if ($artistName) {
                $lastArtistName = $artistName;
            } else {
                $artistName = $lastArtistName;
            }
    
            // Vérification minimale
            if (!$artistName || !$title) continue;
    
            // Recherche ou création de l'artiste
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $artistName]);
            if (!$person) {
                $person = new Person();
                $person->setName($artistName);
                $em->persist($person);
            }
    
            // Recherche ou création de la chanson
            $song = $em->getRepository(Song::class)->findOneBy(['title' => $title]);
            if (!$song) {
                $song = new Song();
                $song->setTitle($title);
            }
    
            $song->addPerson($person);
            $song->setIsDownloaded($downloaded === 'Oui');
            $song->setHasLyrics($hasLyrics === 'Oui');
            $song->setIsListened($listened === 'Oui');
    
            // Logique connaissance utilisateur
            if (!empty($crossValue)) {
                $song->setUserSongKnowledge('by_heart');
            } elseif ($userKnowledgeRaw) {
                $song->setUserSongKnowledge($this->mapKnowledge($userKnowledgeRaw));
            }
    
            // Compter T et C
            $normalPlayCount = substr_count($playInfo, 'T');
            $noplpCount = substr_count($playInfo, 'C');
    
            $song->incrementNormalPlayCount($normalPlayCount);
            $song->incrementNoplpCount($noplpCount);
            $song->incrementSameSongCount((int) $sameSong);
    
            // Gestion de la date dans les colonnes J ou K
            $dateCell1 = $row['I'] ?? null;
            $dateCell2 = $row['J'] ?? null;
    
            $dateToSet = null;
            if (!empty($dateCell1)) {
                $dateToSet = \DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateCell1)));
            } elseif (!empty($dateCell2)) {
                $dateToSet = \DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateCell2)));
            }
    
            if ($dateToSet) {
                $song->setLastReviewDate($dateToSet);
            }
    
            $em->persist($song);
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


