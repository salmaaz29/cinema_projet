<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FilmsRepository;
use App\Repository\SeancesRepository;
use App\Entity\Films;

final class HoraireController extends AbstractController
{
    #[Route('/horaire', name: 'app_horaire')]
public function index(FilmsRepository $filmRepository, SeancesRepository $seanceRepository): Response
{
    $films = $filmRepository->findAll();
    foreach ($films as $film) {
        $film->seances = $seanceRepository->findBy(['film' => $film]);
    }

    return $this->render('horaire/index.html.twig', [
        'films' => $films,
    ]);
}

}
