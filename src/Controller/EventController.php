<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/listEvent', name: 'app_list')]
    public function show(EventRepository $eventRepo): Response
    {
        $results = $eventRepo->findBy(["enabled" => True]);

        return $this->render('event/show.html.twig', [
            'results' => $results,
        ]);
    }
}
