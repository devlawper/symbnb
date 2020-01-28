<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     * @param $page
     * @param Pagination $pagination
     *
     * @return Response
     */
    public function index($page, Pagination $pagination)
    {
        $pagination
            ->setEntityClass(Booking::class)
            ->setCurrentPage($page)
            ->setLimit(15)
        ;

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
//            'bookings' => $pagination->getData(),
//            'pages' => $pagination->getPages(),
//            'page' => $page,
        ]);
    }


    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On met le montant de resersation à 0 et elle sera automtiquement recaclulé au PreUpdate grace à la fonction dans l'Entité
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'booking'   => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
            $manager->remove($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation sur l'annonce <strong>{$booking->getAd()->getTitle()}</strong> a bien été supprimée"
            );

        return $this->redirectToRoute('admin_booking_index');
    }
}
