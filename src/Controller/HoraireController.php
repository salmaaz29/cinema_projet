<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FilmsRepository;
use App\Repository\SeancesRepository;
use App\Entity\Films;

final class HoraireController extends AbstractController
{
    #[Route('/horaire', name: 'app_horaire')]
public function index(Request $request, FilmsRepository $filmRepository, SeancesRepository $seanceRepository): Response
{
    $films = $filmRepository->findAll();
    foreach ($films as $film) {
        $film->seances = $seanceRepository->findBy(['film' => $film]);
    }
    
        // Récupérer le terme de recherche
        $search = $request->query->get('search');

        // Filtrer les films en fonction du terme de recherche
        if ($search) {
            $films = $filmRepository->findByTitre($search);
        } else {
            $films = $filmRepository->findAll();
        }

    return $this->render('horaire/index.html.twig', [
        'films' => $films,
    ]);
}

}
