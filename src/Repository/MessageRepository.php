<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Utilisateur $from
     * @param Utilisateur $to
     * @return Message[] Returns an array of Message objects
     */
    public function findByConversation(Utilisateur $from, Utilisateur $to, $before = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('((m.from_user = :from AND m.to_user = :to) OR (m.from_user = :to AND m.to_user = :from))')
            ;
        if($before){
            $qb->andWhere('(m.created_at < :createdAt)');
        }
        $qb ->setParameter('from', $from)
            ->setParameter('to', $to)
            ;
        if($before){
            $qb->setParameter('createdAt', $before);
        }
        /*            ->setMaxResults(10)*/
        return $qb->orderBy('m.created_at', 'DESC')->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     */
    public function findUnreadCountByUser(Utilisateur $utilisateur){
        $result = $this->getEntityManager()->createQuery(
          "SELECT u.id, count(m) nombre
              FROM App:Message m
              LEFT JOIN App:Utilisateur u WITH (m.from_user = u.id)
              WHERE m.to_user = :utilisateur AND m.read_at IS NULL
              GROUP BY m.from_user"
        )
            ->setParameter('utilisateur', $utilisateur)
            ->getResult();
        return array_column($result, 'nombre', 'id');
    }


    public function readAll(Utilisateur $utilisateur, Utilisateur $from){
        $this->createQueryBuilder('m')
            ->update('App:Message', 'm')
            ->set('m.read_at', ':date')
            ->where('m.from_user = :from AND m.to_user = :to AND m.read_at IS NULL')
            ->setParameter('date', new \DateTime('now'))
            ->setParameter('from', $from)
            ->setParameter('to', $utilisateur)
            ->getQuery()
            ->execute();
    }

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
