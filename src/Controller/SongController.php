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
            // Récupération de l'ID de l'artiste sélectionné et du nouveau nom éventuel
            $personId = $request->request->get('person');
            $newPersonName = $request->request->get('newPerson');

            if ($personId) {
                // Récupération de l'artiste existant
                $person = $entityManager->getRepository(Person::class)->find($personId);
            } elseif ($newPersonName) {
                // Création d'un nouvel artiste si non trouvé
                $person = new Person();
                $person->setName($newPersonName);
                $entityManager->persist($person);
            }

            // Ajout de l'artiste à la chanson si trouvé ou créé
            if (isset($person)) {
                $song->addPerson($person);
            }

            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('app_song'); // Redirection vers la liste des morceaux
        }

        return $this->render('song/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_song_edit')]
    public function edit(Request $request, Song $song, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_song');
        }

        return $this->render('song/edit.html.twig', [
            'form' => $form->createView(),
            'song' => $song,
        ]);
    }
}
