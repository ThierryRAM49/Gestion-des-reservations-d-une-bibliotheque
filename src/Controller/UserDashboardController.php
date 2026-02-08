<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Entity\Reservation;

final class UserDashboardController extends AbstractController
{

    #[Route('/dashboard', name: 'app_dashboard_user')]
    public function userDashboard()
    {
        return $this->render('dashboard/user.html.twig');
    }

    #[Route('/admin', name: 'app_dashboard_admin')]
    public function adminDashboard()
    {
        return $this->render('dashboard/admin.html.twig');
    }


    #[Route('/user/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        return $this->render('user_dashboard/profile.html.twig', [
            'controller_name' => 'UserDashboardController',
        ]);
    }

    #[Route('/books', name: 'app_user_dashboard_books')]
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

    return $this->render('reservation/index.html.twig', [
        'reservations' => $reservationRepository->findBy([
            'user' => $user
        ]),
    ]);
}

#[IsGranted('ROLE_USER')]
#[Route('/books/{id}/reserve', name: 'app_user_dashboard_book_reserve')]
public function reserve(Book $book, EntityManagerInterface $em, ReservationRepository $reservationRepository): Response
{
    $user = $this->getUser();

    // Vérifier si l'utilisateur a déjà réservé ce livre
    $existingReservation = $reservationRepository->findOneBy([
        'user' => $user,
        'book' => $book,
        'status' => 'en cours'
    ]);

    if ($existingReservation) {
        $this->addFlash('warning', 'Vous avez déjà une réservation en cours pour ce livre.');
        return $this->redirectToRoute('app_user_reservations');
    }

    if (!$book->isAvailable()) {
        $this->addFlash('error', 'Ce livre n\'est plus disponible.');
        return $this->redirectToRoute('app_user_dashboard_books');
    }

    $reservation = new Reservation();
    $reservation->setBook($book);
    $reservation->setUser($user);
    $reservation->setDateReservation(new \DateTimeImmutable());
    $reservation->setDateReturn((new \DateTime())->modify('+14 days'));
    $reservation->setStatus('en cours');

    $book->decrementStock();

    $em->persist($reservation);
    $em->flush();

    $this->addFlash('success', 'Votre réservation a été enregistrée.');

    return $this->redirectToRoute('app_user_reservations');
}
}
