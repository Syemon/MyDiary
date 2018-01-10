<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 04.01.18
 * Time: 16:18
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Diary;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiaryRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email", "phoneNumber"}, message="You already have an account")
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
     * @Assert\NotBlank()
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $diary;

    public function __construct()
    {
        $this->diary = new ArrayCollection();
    }

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    private $nickname;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[0-9]*$/", message="Phone number should look like: 111222333")
     * @ORM\Column(type="string", unique=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    public function getUsername()
    {
        return $this->phoneNumber;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname($nickname)
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

    public function getId()
    {
        return $this->id;
    }

}