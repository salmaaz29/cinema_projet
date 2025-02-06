<?php
// src/Controller/TicketController.php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Service\PdfGeneratorService;
use App\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends AbstractController
{
    private $pdfGeneratorService;
    private $entityManager;

    public function __construct(PdfGeneratorService $pdfGeneratorService, EntityManagerInterface $entityManager)
    {
        $this->pdfGeneratorService = $pdfGeneratorService;
        $this->entityManager = $entityManager;
    }

    #[Route('/ticket/{ticketId}/pdf', name: 'generate_ticket_pdf')]
    public function downloadTicket(int $ticketId): Response
    {
        // Récupération du ticket en base de données
        $ticket = $this->entityManager->getRepository(Tickets::class)->find($ticketId);

        // Vérification si le ticket existe
        if (!$ticket) {
            throw $this->createNotFoundException("Le ticket avec l'ID {$ticketId} n'existe pas.");
        }

        // Générer le QR Code
        $qrCodePath = $this->generateQrCode($this->generateUrl('generate_ticket_pdf', [
            'ticketId' => $ticket->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        // Générer le contenu PDF
        $pdfContent = $this->pdfGeneratorService->output('<!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ticket de Réservation</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; background: url("assets/background.png") no-repeat center center fixed; }
                    .ticket { width: 80%; max-width: 800px; margin: 50px auto; border-radius: 10px; padding: 30px; background: rgba(255, 255, 255, 0.9); text-align: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); }
                    .ticket-header { font-size: 24px; font-weight: bold; color: #D0021B; margin-bottom: 20px; }
                    .ticket-details { font-size: 18px; line-height: 1.6; }
                    .icon { width: 20px; height: 20px; vertical-align: middle; margin-right: 10px; }
                    .qr-code { margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class="ticket">
                    <div class="ticket-header"><img src="assets/ticket.png" class="icon" alt="ticket"> Ticket de Réservation - CinéVerse</div>
                    <div class="ticket-details">
                        <p><img src="assets/film.png" class="icon" alt="film"> <strong>Film :</strong> ' . htmlspecialchars($ticket->getSeance()->getFilm()->getTitre()) . '</p>
                        <p><img src="assets/calendar.png" class="icon" alt="date"> <strong>Date & Heure :</strong> ' . $ticket->getSeance()->getDateHeure()->format('d/m/Y H:i') . '</p>
                        <p><img src="assets/money.png" class="icon" alt="prix"> <strong>Prix :</strong> ' . $ticket->getPrix() . ' MAD</p>
                        <p><img src="assets/user.png" class="icon" alt="user"> <strong>Utilisateur :</strong> ' . htmlspecialchars($ticket->getUser()->getNomPrenom()) . '</p>
                        <p><img src="assets/receipt.png" class="icon" alt="achat"> <strong>Date d\'achat :</strong> ' . $ticket->getDateAchat()->format('d/m/Y') . '</p>
                        <p><img src="assets/barcode.png" class="icon" alt="ticket"> <strong>Numéro Ticket :</strong> CV-' . $ticket->getId() . '-' . date('Ymd') . '</p>
                    </div>
                    <div class="qr-code">
                        <img src="' . $qrCodePath . '" alt="QR Code du ticket">
                    </div>
                </div>
            </body>
        </html>');

        // Retourner le PDF dans la réponse
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function generateQrCode(string $data): string
    {
        // Créer le QR Code
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 200,
            margin: 10
        );
    
        // Générer l'image QR en PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
    
        // Chemin absolu vers le dossier de stockage
        $qrCodeDirectory = __DIR__ . '/../../public/assets/qr_codes';
    
        // Créer le dossier si nécessaire
        if (!is_dir($qrCodeDirectory)) {
            mkdir($qrCodeDirectory, 0755, true);
        }
    
        // Nom unique pour le fichier QR Code
        $filePath = $qrCodeDirectory . '/ticket_' . uniqid() . '.png';
    
        // Sauvegarder l'image QR Code dans le dossier
        file_put_contents($filePath, $result->getString());
    
        // Retourner l'URL complète du QR Code
        return $this->generateUrl(
            'qr_code_file',
            ['filename' => basename($filePath)],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
    #[Route('/assets/qr_codes/{filename}', name: 'qr_code_file')]
public function serveQrCode(string $filename): Response
{
    $filePath = __DIR__ . '/../../public/assets/qr_codes/' . $filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Fichier QR Code non trouvé.');
    }

    return new BinaryFileResponse($filePath);
}

    
}
