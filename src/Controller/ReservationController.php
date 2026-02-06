<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/reservation')]
final class ReservationController extends AbstractController
{

    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {

        // ADMIN → toutes les réservations
        if ($this->isGranted('ROLE_ADMIN')) {
            $reservations = $reservationRepository->findAll();
        }
        // USER → uniquement ses réservations
        else {
            $reservations = $reservationRepository->findBy([
                'user' => $this->getUser(),
            ]);
        }
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $reservation->setDateReservation(new \DateTimeImmutable());
        $reservation->setUser($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $reservation->getBook();

            if (!$book->isAvailable()) {
                $this->addFlash('error', 'Ce livre n’est plus disponible.');
                return $this->redirectToRoute('app_book_index');
            }

            // 🔽 décrément du stock
            $book->decrementStock();
            $entityManager->persist($reservation);
            $entityManager->flush();


            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            $reservation->getUser() !== $this->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            $reservation->getUser() !== $this->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/return', name: 'app_reservation_return', methods: ['POST'])]
    public function returnBook(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $reservation->setDateReturn(new \DateTime());
        $reservation->getBook()->incrementStock();

        $em->flush();

        return $this->redirectToRoute('app_reservation_index');
    }
}
