<?php
// src/Controller/AdminDashboardController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TicketsRepository;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(TicketsRepository $ticketsRepository): Response
    {
        // Vérifie si l'utilisateur est un administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer les films les plus demandés
        $films = $ticketsRepository->findMostRequestedFilms();

        // Si l'utilisateur est admin, afficher le tableau de bord de l'admin
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'films' => $films, // Passer les films à la vue
        ]);
    }
}
