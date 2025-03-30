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
        $songs = $songRepository->findAll();

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/song/{id}', name: 'app_song_show')]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/new', name: 'app_song_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les artistes sélectionnés et ceux ajoutés manuellement
            $selectedArtistsIds = $request->request->all('song')['person'] ?? [];
            $newPersonName = $request->request->get('newPerson');
            
            // Ajouter les artistes existants sélectionnés
            $personRepository = $entityManager->getRepository(Person::class);
            foreach ($selectedArtistsIds as $artistId) {
                $existingPerson = $personRepository->find($artistId);
                if ($existingPerson) {
                    $song->addPerson($existingPerson);
                }
            }
    
            // Ajouter un nouvel artiste s'il est renseigné
            if (!empty($newPersonName)) {
                $existingPerson = $personRepository->findOneBy(['name' => $newPersonName]);
                if (!$existingPerson) {
                    $newPerson = new Person();
                    $newPerson->setName($newPersonName);
                    $entityManager->persist($newPerson);
                    $song->addPerson($newPerson);
                } else {
                    $song->addPerson($existingPerson);
                }
            }
    
            $entityManager->persist($song);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_song');
        }
    
        return $this->render('song/new.html.twig', [
            'form' => $form->createView(),
            'artists' => $entityManager->getRepository(Person::class)->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_song_edit')]
    public function edit(Request $request, Song $song, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les artistes sélectionnés et ceux ajoutés manuellement
            $selectedArtistsIds = $request->request->all('song')['person'] ?? [];
            $newPersonName = $request->request->get('newPerson');

            // Ajouter les artistes existants sélectionnés
            $personRepository = $entityManager->getRepository(Person::class);
            foreach ($selectedArtistsIds as $artistId) {
                $existingPerson = $personRepository->find($artistId);
                if ($existingPerson && !$song->getPerson()->contains($existingPerson)) {
                    $song->addPerson($existingPerson);
                }
            }

            // Ajouter un nouvel artiste s'il est renseigné
            if (!empty($newPersonName)) {
                $existingPerson = $personRepository->findOneBy(['name' => $newPersonName]);
                if (!$existingPerson) {
                    $newPerson = new Person();
                    $newPerson->setName($newPersonName);
                    $entityManager->persist($newPerson);
                    $song->addPerson($newPerson);
                } else {
                    if (!$song->getPerson()->contains($existingPerson)) {
                        $song->addPerson($existingPerson);
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_song');
        }

        return $this->render('song/edit.html.twig', [
            'form' => $form->createView(),
            'song' => $song,
            'artists' => $entityManager->getRepository(Person::class)->findAll(),
        ]);
    }
}
