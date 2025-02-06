<?php

namespace App\Controller;
use App\Entity\Tickets;
use App\Entity\Seances;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketsRepository;

final class PaymentController extends AbstractController
{
   
    #[Route('/payment/{ticketId}', name: 'payment')]
    public function payment($ticketId, TicketsRepository $ticketsRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le ticket
        $ticket = $ticketsRepository->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('Ticket non trouvé.');
        }

        // Si le ticket est déjà payé, rediriger
        if ($ticket->getIsPaid()) {
            return $this->redirectToRoute('payment_confirmation', ['ticketId' => $ticket->getId()]);
        }

        // Afficher le formulaire de paiement
        if ($request->isMethod('POST')) {
            // Simuler le processus de paiement ici (valider les informations de paiement)
            // Par exemple : validation d'une carte de crédit
            $isPaymentSuccessful = true; // Cette variable doit être définie selon la réponse du service de paiement réel

            if ($isPaymentSuccessful) {
                // Marquer le ticket comme payé
                $ticket->setIsPaid(true);
                // $entityManager is already injected
                $entityManager->persist($ticket);
                $entityManager->flush();

                // Confirmation du paiement et redirection
                return $this->redirectToRoute('payment_confirmation', ['ticketId' => $ticket->getId()]);
            } else {
                // Gestion de l'échec du paiement
                return $this->redirectToRoute('payment_failed');
            }
        }

        return $this->render('payment/payment_form.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    
    #[Route('/payment/confirmation/{ticketId}', name: 'payment_confirmation')]
    public function paymentConfirmation($ticketId, TicketsRepository $ticketsRepository): Response
    {
        // Récupérer le ticket pour la confirmation
        $ticket = $ticketsRepository->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('Ticket non trouvé.');
        }

        return $this->render('payment/payment_confirmation.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    
    #[Route('/payment/failed', name: 'payment_failed')]
    public function paymentFailed(): Response
    {
        return $this->render('payment/payment_failed.html.twig');
    }
}
