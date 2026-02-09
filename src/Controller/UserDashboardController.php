<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Entity\Reservation;

final class UserDashboardController extends AbstractController
{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(): Response
    {
        return $this->render('user_dashboard/index.html.twig', [
            'controller_name' => 'UserDashboardController',
        ]);
    }

    #[Route('/books', name: 'app_book_index')]
public function books(BookRepository $bookRepository): Response
{
    return $this->render('book/index.html.twig', [
        'books' => $bookRepository->findAll(),
    ]);
}

#[Route('/user/reservations', name: 'app_user_reservations')]
public function reservations(ReservationRepository $reservationRepository): Response
{
    $user = $this->getUser();

    return $this->render('reservation/my.html.twig', [
        'reservations' => $reservationRepository->findBy([
            'user' => $user
        ]),
    ]);
}

#[Route('/books/{id}/reserve', name: 'app_book_reserve')]
public function reserve(Book $book, EntityManagerInterface $em): Response
{
    $reservation = new Reservation();
    $reservation->setBook($book);
    $reservation->setUser($this->getUser());
    $reservation->setDateReservation(new \DateTimeImmutable());
    $reservation->setDateReturn((new \DateTime())->modify('+14 days'));
    $reservation->setStatus('en cours');

    $em->persist($reservation);
    $em->flush();

    return $this->redirectToRoute('app_user_reservations');
}
}
