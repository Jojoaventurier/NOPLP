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

    #[Route('/new/', name: 'app_song_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);
    
        $personRepository = $entityManager->getRepository(Person::class);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // Better to use form data instead of raw request
            
            // Handle existing artists
            $existingIds = $request->request->all('song')['existingPersons'] ?? [];
            foreach ($existingIds as $id) {
                if (is_numeric($id)) {
                    $person = $personRepository->find($id);
                    if ($person) {
                        $song->addPerson($person);
                    }
                }
            }
    
            // Handle new artists
            $newNames = $request->request->all('song')['newPersons'] ?? [];
            foreach ($newNames as $name) {
                $name = trim($name);
                if ($name !== '') {
                    $person = $personRepository->findOneBy(['name' => $name]);
                    if (!$person) {
                        $person = new Person();
                        $person->setName($name);
                        $entityManager->persist($person);
                        // No flush here to avoid partial persistence
                    }
                    $song->addPerson($person);
                }
            }
    
            $entityManager->persist($song);
            $entityManager->flush();
    
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

    // Récupère les données envoyées du champ "song[person][]"
    $personsData = $request->request->all('song')['person'] ?? [];

    if ($form->isSubmitted() && $form->isValid()) {
        // On vide les artistes liés pour éviter les doublons
        foreach ($song->getPerson() as $existingPerson) {
            $song->removePerson($existingPerson);
        }

        foreach ($personsData as $value) {
            if (str_starts_with($value, 'new_')) {
                $name = substr($value, 4);
                $name = html_entity_decode(str_replace(['&nbsp;', '&comma;'], [' ', ','], $name));
                $name = strip_tags($name);

                $existingPerson = $entityManager->getRepository(Person::class)->findOneBy(['name' => $name]);
                if ($existingPerson) {
                    $song->addPerson($existingPerson);
                } else {
                    $newPerson = new Person();
                    $newPerson->setName($name);
                    $entityManager->persist($newPerson);
                    $song->addPerson($newPerson);
                }
            } else {
                $person = $entityManager->getRepository(Person::class)->find($value);
                if ($person) {
                    $song->addPerson($person);
                }
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
    ]);
}
}
