<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    /**
     * @Route("/", name="application")
     * @param ObjectManager $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ObjectManager $em)
    {
        $utilisateur= $this->getUser();
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
        return $this->render('app/homepage.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * @Route("/conversations/{id}", name="show_conversation")
     * @param ObjectManager $em
     * @param Utilisateur $destinataire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showConversation(ObjectManager $em, Utilisateur $destinataire)
    {
        $utilisateur= $this->getUser();
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
        return $this->render('app/homepage.html.twig', [
            'utilisateurs' => $utilisateurs,
            'destinataire' => $destinataire
        ]);
    }
}
