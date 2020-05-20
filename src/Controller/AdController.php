<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
// use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    /**
     * Permet de créer une annonce
     * Utilisation du ManagerRegistry au lieu du ObjectManager
     * @Route("ads/new", name="ads_create")
     */

    public function create(Request $request, ManagerRegistry $managerRegistry)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $em = $managerRegistry->getManager();
                $em->persist($image);
            }
            // $manager->getDoctrine()->getManager();
            $em = $managerRegistry->getManager();
            $em->persist($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong>a bien été enregistrée !"
            ) ;
 
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);

            // $manager->persist($ad);
            // $manager->flush();
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'edition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ManagerRegistry $managerRegistry){

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $em = $managerRegistry->getManager();
                $em->persist($image);
            }
            // $manager->getDoctrine()->getManager();
            $em = $managerRegistry->getManager();
            $em->persist($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong>a bien été modifiée!"
            ) ;
 
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);

            
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);

    }
    /**
     * Permet de montrer une seule annonce
     * Utilisation de ParamConverter, on peut soit injecter $slug ou non comme argument de la fonction
     * 
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show(Ad $ad)
    {
        // je récupère l'annonce qui correspond au slug
        // $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);

    }
    /**
     * Permet de lister toutes les annonces
     * 
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();


        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    

}
