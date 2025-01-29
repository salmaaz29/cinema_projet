<?php

namespace App\Controller;

use App\Entity\Films;
use App\Form\FilmsType;
use App\Repository\FilmsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/films')]
final class AdminFilmsController extends AbstractController
{
    #[Route(name: 'app_admin_films_index', methods: ['GET'])]
    public function index(FilmsRepository $filmsRepository): Response
    { // affiche liste de tous les films diponibles 
        return $this->render('admin_films/index.html.twig', [
            'films' => $filmsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_films_new', methods: ['GET', 'POST'])] // creation d'un nouveau film
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Films();
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_films_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_films/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_films_show', methods: ['GET'])] //Afficher les details d'un film 
    public function show(Films $film): Response
    {
        return $this->render('admin_films/show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_films_edit', methods: ['GET', 'POST'])] // modifier les details d'un film
    public function edit(Request $request, Films $film, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_films_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_films/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_films_delete', methods: ['POST'])] // supprimer un film
    public function delete(Request $request, Films $film, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_films_index', [], Response::HTTP_SEE_OTHER);
    }
}
