<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiaryRepository")
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $note;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $attachment;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    public function getNote()
    {
        return $this->note;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }

    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}