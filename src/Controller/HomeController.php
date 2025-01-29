<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UsersRepository;
use App\Entity\Films;
use App\Form\UtilisateurType;
use App\Repository\FilmsRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HomeController extends AbstractController
{// name c est identifiant unique  de chaque route ne pas avoir meme nom de 2 routes
    #[Route('/home', name: 'app_pre_controller')]   // associe l url a la fct qui va etre executer --si j'envoie une requette /first je peut repondre par cette fct 
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer tous les films depuis la base de données
        $films = $em->getRepository(Films::class)->findAll();
        return $this->render('homecontroller/home.html.twig', [
            'films' => $films,
        ]);
    }

    #[Route('/affiche/{id<\d+>}', name: 'user_affiche')]   // associe l url a la fct qui va etre executer --si j'envoie une requette /first je peut repondre par cette fct 
    public function affiche(UsersRepository $user): Response
    {
        // $user = $repository->find($id);
        // if($user == null){
        //    throw $this->createNotFoundException(' Utilisateur not found');
        // }
        return $this->render('affiche.html.twig',
        ['user' => $user]);
    }
    #[Route('/user/new', name: 'new_user')]
    public function new(): Response
    {
        $form = $this->createForm(UtilisateurType::class);
       return $this->render('new.html.twig',
    ['form'=> $form]); 
    }


}
