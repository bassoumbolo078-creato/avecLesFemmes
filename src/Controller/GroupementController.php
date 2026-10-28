<?php

namespace App\Controller;

use App\Service\GroupementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupementController extends AbstractController
{
    public function __construct(
        private readonly GroupementService $groupementService,
    ) {
    }

    #[Route('/groupement', name: 'app_groupement')]
    public function index(): Response
    {
        return $this->render('groupement/index.html.twig', [
            'data_groupements' => $this->groupementService->lister(),
        ]);
    }
}