<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Person;
use App\Form\SongType;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SongController extends AbstractController
{
    #[Route('/song', name: 'app_song')]
    public function index(SongRepository $songRepository): Response
    {   
        $songs = $songRepository->findAllWithDetails();
        
        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/song/{id}', name: 'app_song_show')]
    public function show(Song $song): Response
    {

        $mainPerformer = $song->getPerson()->first() ?: null;

        $otherSongs = [];
    
        if ($mainPerformer) {
            $otherSongs = $mainPerformer->getSongs()->filter(function(Song $s) use ($song) {
                return $s !== $song;
            });
        }

        return $this->render('song/show.html.twig', [
            'song' => $song,
            'main_performer' => $mainPerformer,
            'other_songs' => $otherSongs,
        ]);
    }

    #[Route('/new/', name: 'app_song_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);
    
        $personRepository = $entityManager->getRepository(Person::class);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $request->request->all('song');
            $existingIds = $submittedData['existingPersons'] ?? [];
            $newNames = $submittedData['newPersons'] ?? [];
    
            // Handle file upload
            $uploadedFile = $form->get('lyricsFile')->getData();
            if ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'txt') {
                try {
                    $content = file_get_contents($uploadedFile->getPathname());
                    $song->setLyrics($content);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la lecture du fichier de paroles.');
                }
            }
    
            // Handle existing artists
            foreach ($existingIds as $id) {
                if (is_numeric($id)) {
                    $person = $personRepository->find($id);
                    if ($person) {
                        $song->addPerson($person);
                    }
                }
            }
    
            // Handle new artists with normalized name checking
            foreach ($newNames as $name) {
                $normalizedName = $this->normalizeName($name);
                if ($normalizedName !== '') {
                    // Check if artist exists (case-insensitive and whitespace normalized)
                    $person = $personRepository->createQueryBuilder('p')
                        ->where('LOWER(TRIM(p.name)) = LOWER(:name)')
                        ->setParameter('name', $normalizedName)
                        ->getQuery()
                        ->getOneOrNullResult();
    
                    if (!$person) {
                        $person = new Person();
                        $person->setName($this->normalizeName($name)); // Store normalized name
                        $person->setCategory('Femme'); // Set default category
                        $entityManager->persist($person);
                    }
                    $song->addPerson($person);
                }
            }
    
            $entityManager->persist($song);
            $entityManager->flush();
            $this->addFlash('success', 'La chanson a été ajoutée avec succès.');
    
            return $this->redirectToRoute('app_song');
        }
    
        return $this->render('song/new.html.twig', [
            'form' => $form->createView(),
            'artists' => $personRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_song_edit')]
    public function edit(Request $request, Song $song, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);
        
        $submittedData = $request->request->all('song');
        $existingIds = $submittedData['existingPersons'] ?? [];
        $newNames = $submittedData['newPersons'] ?? [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $uploadedFile = $form->get('lyricsFile')->getData();
            if ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'txt') {
                try {
                    $content = file_get_contents($uploadedFile->getPathname());
                    $song->setLyrics($content);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la lecture du fichier de paroles.');
                }
            }
    
            // Clear existing associations
            foreach ($song->getPerson() as $existingPerson) {
                $song->removePerson($existingPerson);
            }
    
            // Handle existing artists
            foreach ($existingIds as $id) {
                if (is_numeric($id)) {
                    $person = $entityManager->getRepository(Person::class)->find($id);
                    if ($person) {
                        $song->addPerson($person);
                    }
                }
            }
    
            // Handle new artists with normalized name checking
            foreach ($newNames as $name) {
                $normalizedName = $this->normalizeName($name);
                if ($normalizedName !== '') {
                    // Check if artist exists (case-insensitive and whitespace normalized)
                    $person = $entityManager->getRepository(Person::class)
                        ->createQueryBuilder('p')
                        ->where('LOWER(TRIM(p.name)) = LOWER(:name)')
                        ->setParameter('name', $normalizedName)
                        ->getQuery()
                        ->getOneOrNullResult();
    
                    if (!$person) {
                        $person = new Person();
                        $person->setName($this->normalizeName($name)); // Store normalized name
                        $person->setCategory('Femme'); // Set default category
                        $entityManager->persist($person);
                    }
                    $song->addPerson($person);
                }
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'La chanson a bien été mise à jour.');
            return $this->redirectToRoute('app_song');
        }
        
        return $this->render('song/edit.html.twig', [
            'form' => $form->createView(),
            'song' => $song,
            'artists' => $entityManager->getRepository(Person::class)->findAll(),
            'songArtists' => $song->getPerson()->toArray(),
        ]);
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name); // Replace multiple spaces with single
        return $name;
    }

}
