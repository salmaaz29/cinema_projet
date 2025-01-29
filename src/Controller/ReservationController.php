<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeancesRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ReservationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/reservation/{id_seance}', name: 'app_reservation')]
    public function index(Int $id_seance, SeancesRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->find($id_seance);

    if (!$seance) {
        throw $this->createNotFoundException('Séance non trouvée');
    }

    return $this->render('reservation/index.html.twig', [
        'seance' => $seance,
    ]);
    }

    #[Route('/finaliser_reservation/{id_seance}', name: 'finaliser_reservation', methods: ['POST'])]
    public function finaliserReservation(int $id_seance, SeancesRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->find($id_seance);

        if (!$seance) {
            throw $this->createNotFoundException('Séance non trouvée');
        }

        if ($seance->getPlacesDisponibles() > 0) {
            // Mettre à jour le nombre de places disponibles
            $seance->setPlacesDisponibles($seance->getPlacesDisponibles() - 1);

            // Sauvegarder les modifications en base de données
            $this->entityManager->flush();

            return $this->redirectToRoute('reservation_success', [
                'id_seance' => $id_seance
            ]);
        } else {
            return $this->render('reservation/error.html.twig', [
                'message' => 'Désolé, il n\'y a plus de places disponibles.',
            ]);
        }
    }

    #[Route('/reservation/success/{id_seance}', name: 'reservation_success')]
    public function success(int $id_seance, SeancesRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->find($id_seance);

        return $this->render('reservation/success.html.twig', [
            'seance' => $seance,
        ]);
    }





}
