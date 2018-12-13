<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiaryRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="diary")
 */
class Diary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="diary")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $note;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $attachment;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param $note
     */
    public function setNote($note) :void
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param $attachment
     */
    public function setAttachment($attachment) :void
    {
        $this->attachment = $attachment;
    }

    public function getCreatedAt() :\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) :void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function setCreatedAtValue() :void
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return int
     */
    public function getId() :int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser() :User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) :void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title) :void
    {
        $this->title = $title;
    }
}
