<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messages_envoyes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $from_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messages_recus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $to_user;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $read_at;

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
