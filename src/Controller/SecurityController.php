<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    // Récupère l'espace choisi via un paramètre de requête
    $space = $request->query->get('space');

    // Récupère l'erreur de connexion s'il y en a une
    $error = $authenticationUtils->getLastAuthenticationError();
    
    // Récupère le dernier nom d'utilisateur saisi
    $lastUsername = $authenticationUtils->getLastUsername();

    // Redirige vers la vue de connexion appropriée selon l'espace choisi
    if ($space === 'admin') {
        return $this->render('security/login1.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    // Si l'espace n'est pas spécifié ou c'est un espace utilisateur, charge la vue de connexion classique
    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error
    ]);
}


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
