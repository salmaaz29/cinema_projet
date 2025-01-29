<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EspaceChoixController extends AbstractController
{
    #[Route('/espace/choix', name: 'app_espace_choix')]
    public function index(): Response
    {
        return $this->render('espace_choix/index.html.twig');
    }
}
