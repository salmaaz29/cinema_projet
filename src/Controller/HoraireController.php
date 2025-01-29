<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeancesRepository;

final class HoraireController extends AbstractController
{
    #[Route('/horaire', name: 'app_horaire')]
    public function index(SeancesRepository $seanceRepository): Response
    {
        $seances = $seanceRepository->findAll();
        return $this->render('horaire/index.html.twig', [
           'seances' => $seances,
        ]);
    }
}
