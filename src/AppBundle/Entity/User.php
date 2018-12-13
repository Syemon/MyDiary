<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiaryRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Diary", mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $diary;

    public function __construct()
    {
        $this->diary = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", unique=false)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive = false;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->phoneNumber;
    }

    /**
     * @return array
     */
    public function getRoles() :array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials() :void
    {
        $this->plainPassword = null;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) :void
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) :void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword) :void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @param array|string $roles
     */
    public function setRoles($roles) :void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber) :void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param $nickname
     */
    public function setNickname($nickname) :void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return ArrayCollection|Diary[]
     */
    public function getDiary()
    {
        return $this->diary;
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    /**
     * @return int
     */
    public function getId() :int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken) :void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return bool
     */
    public function getIsActive() :bool
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive) :void
    {
        $this->isActive = $isActive;
    }

}
