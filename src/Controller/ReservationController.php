<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeancesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tickets;
use DateTimeImmutable;

final class ReservationController extends AbstractController
{
   
    #[Route('/reserver/{seanceId}', name: 'reserver_ticket')]
    
    public function reserver($seanceId, SeancesRepository $seancesRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la séance
        $seance = $seancesRepository->find($seanceId);

        if (!$seance) {
            throw $this->createNotFoundException('Séance non trouvée.');
        }

        // Vérifier s'il reste des places disponibles
        if ($seance->getPlacesDisponibles() <= 0) {
            return $this->redirectToRoute('no_available_seats');
        }

       // Vérifier si l'utilisateur est connecté
$user = $this->getUser();

if (!$user) {
    throw new \Exception('L\'utilisateur doit être connecté pour réserver un ticket.');
}

// Créer une nouvelle instance du ticket
$ticket = new Tickets();
$ticket->setSeance($seance);
$ticket->setUser($user);  // Assurer que l'utilisateur est bien défini
$ticket->setDateAchat(new \DateTimeImmutable());

// Définir le prix du ticket en fonction du prix de la séance
$prixSeance = $seance->getPrix();
$ticket->setPrix($prixSeance);

// Sauvegarder le ticket
$entityManager->persist($ticket);
$entityManager->flush();
        // Réduire le nombre de places disponibles
        $seance->setPlacesDisponibles($seance->getPlacesDisponibles() - 1);
        $entityManager->flush();

        // Rediriger vers la page de paiement
        return $this->redirectToRoute('payment', ['ticketId' => $ticket->getId()]);
    }


}
