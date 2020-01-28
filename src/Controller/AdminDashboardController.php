<?php

namespace App\Controller;

use App\Service\Stats;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     *
     * @param Stats $statsService
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function index(Stats $statsService, EntityManagerInterface $manager)
    {
//        $users = $statsService->getUsersCount();
//        $ads = $statsService->getAdsCount();
//        $bookings = $statsService->getBookingsCount();
//        $comments = $statsService->getCommentsCount();

        $stats    = $statsService->getStats();
        $bestAds  = $statsService->getAdsStats('DESC');
        $worstAds = $statsService->getAdsStats('ASC');

        dump($bestAds);

        return $this->render('admin/dashboard/index.html.twig', [
//            'stats' => compact('users', 'ads', 'bookings', 'comments'),
            'stats'    => $stats,
            'bestAds'  => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
