<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 */
class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="from_user", orphanRemoval=true)
     */
    private $messages_envoyes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="to_user", orphanRemoval=true)
     */
    private $messages_recus;

    public function __construct()
    {
        parent::__construct();
        $this->messages_envoyes = new ArrayCollection();
        $this->messages_recus = new ArrayCollection();
        $this->roles = array("ROLE_USER");
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesEnvoyes(): Collection
    {
        return $this->messages_envoyes;
    }

    public function addMessagesEnvoye(Message $messagesEnvoye): self
    {
        if (!$this->messages_envoyes->contains($messagesEnvoye)) {
            $this->messages_envoyes[] = $messagesEnvoye;
            $messagesEnvoye->setFromUser($this);
        }

        return $this;
    }

    public function removeMessagesEnvoye(Message $messagesEnvoye): self
    {
        if ($this->messages_envoyes->contains($messagesEnvoye)) {
            $this->messages_envoyes->removeElement($messagesEnvoye);
            // set the owning side to null (unless already changed)
            if ($messagesEnvoye->getFromUser() === $this) {
                $messagesEnvoye->setFromUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesRecus(): Collection
    {
        return $this->messages_recus;
    }

    public function addMessagesRecus(Message $messagesRecus): self
    {
        if (!$this->messages_recus->contains($messagesRecus)) {
            $this->messages_recus[] = $messagesRecus;
            $messagesRecus->setToUser($this);
        }

        return $this;
    }

    public function removeMessagesRecus(Message $messagesRecus): self
    {
        if ($this->messages_recus->contains($messagesRecus)) {
            $this->messages_recus->removeElement($messagesRecus);
            // set the owning side to null (unless already changed)
            if ($messagesRecus->getToUser() === $this) {
                $messagesRecus->setToUser(null);
            }
        }

        return $this;
    }
}
