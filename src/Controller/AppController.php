<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Form\MessageType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Route("/conversations/{id}")
     * @Route("/utilisateurs-en-ligne")
     * @param ObjectManager $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ObjectManager $em)
    {
        $utilisateur= $this->getUser();
        $unread = $em->getRepository(Message::class)->findUnreadCountByUser($utilisateur);
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
        return $this->render('app/homepage.html.twig', [
            'utilisateurs' => $utilisateurs,
            'unread' => $unread
        ]);
    }
}
