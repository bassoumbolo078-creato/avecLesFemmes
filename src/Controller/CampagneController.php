<?php

namespace App\Controller;

use App\Service\CampagneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneController extends AbstractController
{
    public function __construct(
        private readonly CampagneService $campagneService,
    ) {
    }

    #[Route('/campagne/{id}', name: 'app_campagne_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        $campagne = $this->campagneService->detail($id);
        if (null === $campagne) {
            throw $this->createNotFoundException('Campagne introuvable.');
        }

        return $this->render('campagne/detail.html.twig', [
            'data_campagne' => $campagne,
        ]);
    }
}