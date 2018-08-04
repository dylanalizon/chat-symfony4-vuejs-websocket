<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends Controller
{
    private $serializer;

    /**
     * ApiController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @Route("/api/conversations", name="api_get_conversations")
     * @param ObjectManager $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConversations(ObjectManager $em)
    {
        $utilisateur= $this->getUser();
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
        return $this->json([
            "conversations" => $utilisateurs
        ]);
    }
}
