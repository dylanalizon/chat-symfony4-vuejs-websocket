<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/api/conversations", methods={"GET"}, name="api_get_conversations")
     * @param ObjectManager $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConversations(ObjectManager $em)
    {
        $utilisateur= $this->getUser();
        $conversations = $em->getRepository(Utilisateur::class)->findAllWithoutCurrentUser($utilisateur->getId());
        $unread = $em->getRepository(Message::class)->findUnreadCountByUser($utilisateur);
        foreach ($conversations as $key => $conversation) {
            if(isset($unread[$conversation['id']])){
                $conversations[$key]['unread'] = $unread[$conversation['id']];
            }else{
                $conversations[$key]['unread'] = 0;
            }
        }
        return $this->json([
            "conversations" => $conversations
        ]);
    }

    /**
     * @Route("/api/conversations/{id}", methods={"POST"}, name="api_post_conversations")
     * @param Utilisateur $destinataire
     * @param ObjectManager $em
     * @param Request $request
     * @param PredisClient $redis
     * @return JsonResponse|Response
     */
    public function postConversations(Utilisateur $destinataire, ObjectManager $em, Request $request)
    {
        $contentRequest = $request->getContent();
        if(!empty($contentRequest)){
            $params = json_decode($contentRequest, true);
            $token = $params['token'];
            $content = $params['content'];
            $utilisateur = $this->getUser();
            if($this->isCsrfTokenValid($utilisateur->getId(), $token)){
                if(!empty($content)){
                    $message = new Message();
                    $message->setToUser($destinataire)
                        ->setFromUser($utilisateur)
                        ->setContent($content);
                    $em->persist($message);
                    $em->flush();
                    $messagesJson = $this->serializer->serialize(["message" => $message], 'json', ['groups' => ['messages']]);
                    return new Response($messagesJson);
                }else{
                    return $this->json(["message" => "Le contenu du message ne doit pas être vide" ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }else{
                return $this->json(["message" => "Le token est invalide" ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return $this->json(["message" => "La requête est vide" ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/conversations/{id}", name="api_get_conversation")
     * @param Utilisateur $destinataire
     * @param ObjectManager $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConversation(Utilisateur $destinataire, ObjectManager $em, Request $request)
    {
        $utilisateur = $this->getUser();
        if($utilisateur == $destinataire){
            return $this->json(["message" => "erreur" ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }else {
            $before = $request->query->get('before');
            if($before){
                $messagesQuery = $em->getRepository(Message::class)->findByConversation($utilisateur, $destinataire, $before);
            }else{
                $messagesQuery = $em->getRepository(Message::class)->findByConversation($utilisateur, $destinataire);
                $countAll = count($messagesQuery);
            }
            $messages = array_reverse(array_slice($messagesQuery, 0, 10));
            $updated = false;
            foreach ($messages as $message) {
                if($message->getReadAt() === null && $message->getToUser() == $utilisateur){
                    $message->setReadAt(new \DateTime('now'));
                    if($updated === false){
                        $em->getRepository(Message::class)->readAll($utilisateur, $destinataire);
                    }
                    $updated = true;
                }
            }
            $messagesJson = $this->serializer->serialize([
                "messages" => $messages,
                'count' => $before ? '' : $countAll
            ], 'json', ['groups' => ['messages']]);
            return new Response($messagesJson);
        }
    }

    /**
     * @Route("/api/messages/{id}", methods={"POST"}, name="api_post_read_message")
     * @param Message $message
     * @return JsonResponse
     */
    public function postReadMessage(Message $message, ObjectManager $em){
        $user = $this->getUser();
        if ($user == $message->getToUser()) {
            $message->setReadAt(new \DateTime('now'));
            $em->persist($message);
            $em->flush();
        }else{
            return $this->json(["message" => "Mauvais destinataire" ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json(['read_at' => $message->getReadAt()]);
    }
}
