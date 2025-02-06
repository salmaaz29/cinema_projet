<?php

namespace App\Controller;

use App\Entity\Seances;
use App\Form\SeancesType;
use App\Repository\SeancesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/seances')]
final class AdminSeancesController extends AbstractController
{
    #[Route(name: 'app_admin_seances_index', methods: ['GET'])]
    public function index(SeancesRepository $seancesRepository): Response
    {
        return $this->render('admin_seances/index.html.twig', [
            'seances' => $seancesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_seances_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seances();
        $form = $this->createForm(SeancesType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_seances_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_seances/new.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_seances_show', methods: ['GET'])]
    public function show(Seances $seance): Response
    {
        return $this->render('admin_seances/show.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_seances_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seances $seance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeancesType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_seances_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_seances/edit.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_seances_delete', methods: ['POST'])]
    public function delete(Request $request, Seances $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seance->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_seances_index', [], Response::HTTP_SEE_OTHER);
    }
}
