<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"messages"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messages_envoyes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"messages"})
     */
    private $from_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messages_recus")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"messages"})
     */
    private $to_user;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"messages"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"messages"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"messages"})
     */
    private $read_at;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->read_at = null;
    }


    /**
     *
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->created_at == null) {
            $this->created_at = new \DateTime('now');
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFromUser(): ?Utilisateur
    {
        return $this->from_user;
    }

    public function setFromUser(?Utilisateur $from_user): self
    {
        $this->from_user = $from_user;

        return $this;
    }

    public function getToUser(): ?Utilisateur
    {
        return $this->to_user;
    }

    public function setToUser(?Utilisateur $to_user): self
    {
        $this->to_user = $to_user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->read_at;
    }

    public function setReadAt(?\DateTimeInterface $read_at): self
    {
        $this->read_at = $read_at;

        return $this;
    }
}
