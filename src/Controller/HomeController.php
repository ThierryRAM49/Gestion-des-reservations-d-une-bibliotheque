<?php

// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        BookRepository $bookRepository,
        ReservationRepository $reservationRepository
    ): Response {

     // Utilisateur connecté
    $user = $this->getUser();

        // 5 derniers livres
        $latestBooks = $bookRepository->findBy([], ['id' => 'DESC'], 5);

        // Réservations de l'utilisateur connecté uniquement
    $myReservations = [];

    if ($user) {
        $myReservations = $reservationRepository->findBy([
            'user' => $user
        ]);
    }

    return $this->render('home/index.html.twig', [
        'latestBooks' => $latestBooks,
        'myReservations' => $myReservations,
        'user' => $user
    ]);
}
}
