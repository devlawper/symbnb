<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     *
     * @param AdRepository $repo
     *
     * @param int $page
     * @return Response
     */
    public function index(AdRepository $repo, $page, Pagination $pagination)
    {
/*        ///////////////////////////////
        // Méthode find() qui permet de retrouver un enregistremeent par son identifiant
        $ad = $repo->find(312);
        dump($ad);

        $ad = $repo->findOneBy([
            'title' => 'Annonce corrigée'
        ]);
        dump($ad);

        $ad = $repo->findBy([], [], 5, 0);
        dump($ad);
        ///////////////////////////////

        $limit = 10;

        //          1   *   10  -   10  =   0
        //          2   *   10  -   10  =   10
        $start = $page * $limit - $limit;

        $total = count($repo->findAll());

        $pages = ceil($total / $limit); // arrondi au dessus*/

        $pagination
            ->setEntityClass(Ad::class)
            ->setCurrentPage($page)
        ;

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
//            'ads'=> $repo->findBy([], [], $limit, $start),
//            'pages' => $pages,
//            'page' => $page,
        ]);
    }


    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad'   => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if (count($ad->getBookings()) > 0 ) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations !"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
