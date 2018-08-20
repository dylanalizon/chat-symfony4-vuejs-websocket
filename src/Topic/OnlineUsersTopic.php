<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 08/08/18
 * Time: 01:41
 */

namespace App\Topic;

use App\Entity\Utilisateur;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class OnlineUsersTopic implements TopicInterface
{
    protected $clientManipulator;

    private $users = [];

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
    }

    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        if (gettype($user) == "object" && get_class($user) == Utilisateur::class) {
            $userId = $user->getId();
            $userInfo = ['id' => $userId, 'username' => $user->getUsername()];
            $this->users[$userId] = $userInfo;
            $topic->broadcast(['online_users' => array_values($this->users)]);
        }
    }

    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        if (gettype($user) == "object" && get_class($user) == Utilisateur::class) {
            unset($this->users[$user->getId()]);
            $topic->broadcast(['online_users' => array_values($this->users)]);
        }
/*        $users = [];
        foreach ($topic as $c) {
            $u = $this->clientManipulator->getClient($c);
            if (gettype($u) == "object" && get_class($u) == Utilisateur::class) {
                $users[] = ['id' => $u->getId(), 'username' => $u->getUsername()];
            }
        }
        $topic->broadcast(['online_users' => $users]);*/
    }

    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param  array $exclude
     * @param  array $eligible
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $topic->broadcast([
            'message' => $event,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app.users';
    }
}