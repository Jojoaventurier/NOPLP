<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Song;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

final class SongImportController extends AbstractController
{
    #[Route('/import-songs', name: 'import_songs', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('excel_file');

        if (!$file) {
            $this->addFlash('error', 'Aucun fichier fourni');
            return $this->redirectToRoute('some_route');
        }

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $rowCount = 0;

        foreach ($rows as $index => $row) {
            if ($index === 1) continue; // Skip header

            $artistName = $row['A'] ?? null;
            $title = $row['B'] ?? null;
            $downloaded = $row['C'] ?? null;
            $hasLyrics = $row['D'] ?? null;
            $listened = $row['E'] ?? null;
            $userKnowledgeRaw = $row['F'] ?? null;
            $crossValue = $row['G'] ?? null;
            $playInfo = strtoupper(trim($row['H'] ?? ''));
            $sameSong = $row['I'] ?? 0;

            if (!$artistName || !$title) continue;

            $person = $em->getRepository(Person::class)->findOneBy(['name' => $artistName]);
            if (!$person) {
                $person = new Person();
                $person->setName($artistName);
                $em->persist($person);
            }

            $song = $em->getRepository(Song::class)->findOneBy(['title' => $title]);
            if (!$song) {
                $song = new Song();
                $song->setTitle($title);
            }

            $song->addPerson($person);
            $song->setIsDownloaded($downloaded === 'Oui');
            $song->setHasLyrics($hasLyrics === 'Oui');
            $song->setIsListened($listened === 'Oui');

            // Logic for user song knowledge
            if (!empty($crossValue)) {
                $song->setUserSongKnowledge('by_heart');
            } elseif ($userKnowledgeRaw) {
                $song->setUserSongKnowledge($this->mapKnowledge($userKnowledgeRaw));
            }

            // Count T and C in play info
            $normalPlayCount = substr_count($playInfo, 'T');
            $noplpCount = substr_count($playInfo, 'C');

            $song->incrementNormalPlayCount($normalPlayCount);
            $song->incrementNoplpCount($noplpCount);
            $song->incrementSameSongCount($sameSong);

            $em->persist($song);
            $rowCount++;
        }

        $em->flush();

        $this->addFlash('success', "$rowCount chansons importées avec succès.");
        return $this->redirectToRoute('some_route');
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
}


