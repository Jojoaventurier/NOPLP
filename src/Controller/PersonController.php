<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PersonController extends AbstractController
{
    #[Route('/person', name: 'app_person')]
    public function index(PersonRepository $personRepository): Response
    {
        $persons = $personRepository->findAll(); // ou trier si besoin
        return $this->render('person/indexBis.html.twig', [
            'persons' => $persons,
        ]);
    }


    #[Route('/artist/{id}', name: 'app_person_show')]
    public function show(Person $person): Response
    {
        // $person récupère l'entité automatiquement via ParamConverter

        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }

       
}
