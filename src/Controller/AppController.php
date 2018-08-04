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
     * @Route("/", name="application")
     * @Route("/conversations/{id}", name="application")
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

/*  /**
     * @Route("/conversations/{id}", name="show_conversation")
     * @param Request $request
     * @param ObjectManager $em
     * @param Utilisateur $destinataire
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\LogicException
    public function showConversation(Request $request, ObjectManager $em, Utilisateur $destinataire)
    {
        $utilisateur= $this->getUser();
        if($utilisateur == $destinataire){
            return $this->redirectToRoute('application');
        }else{
            $utilisateurs = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
            $message = new Message();
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $message->setFromUser($utilisateur)
                    ->setToUser($destinataire);
                $em->persist($message);
                $em->flush();
                unset($message);
                unset($form);
                $message = new Message();
                $form = $this->createForm(MessageType::class, $message);
            }
            $messages = $em->getRepository(Message::class)->findByConversation($utilisateur, $destinataire);
            $unread = $em->getRepository(Message::class)->findUnreadCountByUser($utilisateur);
            if(isset($unread[$destinataire->getId()])){
                $em->getRepository(Message::class)->readAll($utilisateur, $destinataire);
                unset($unread[$destinataire->getId()]);
            }
            return $this->render('app/homepage.html.twig', [
                'utilisateurs' => $utilisateurs,
                'destinataire' => $destinataire,
                'form' => $form->createView(),
                'messages' => $messages,
                'unread' => $unread
            ]);
        }
    }*/
}
