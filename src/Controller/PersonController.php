<?php

namespace App\Controller;

use App\Repository\PersonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PersonController extends AbstractController
{
    #[Route('/person', name: 'app_person')]
    public function index(): Response
    {
        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }

    #[Route('/search/person', name: 'search_person', methods: ['GET'])]
    public function search(Request $request, PersonRepository $personRepository): JsonResponse
    {
        $query = $request->query->get('q');
        $persons = $personRepository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', "%$query%")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->json(array_map(fn($p) => ['id' => $p->getId(), 'text' => $p->getName()], $persons));
    }
}
