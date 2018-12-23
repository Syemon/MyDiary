<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Swift_Mailer;
use Swift_Transport;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

abstract class AbstractEmailMessage
{
    protected $mailerTransport;
    protected $mailerUser;
    protected $mailerMessage;

    protected $subject;
    protected $addresseeEmail;
    protected $twig;
    protected $body;
    protected $tokenStorage;
    protected $parameters;
    protected $view;
    /** @var User $user */
    protected $user;

    /**
     * @param string $mailerUser
     * @param Swift_Transport $mailerTransport
     * @param Swift_Mailer $mailerMessage
     * @param EngineInterface $twig
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        string $mailerUser,
        Swift_Transport $mailerTransport,
        Swift_Mailer $mailerMessage,
        EngineInterface $twig,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->mailerUser = $mailerUser;
        $this->mailerTransport = $mailerTransport;
        $this->mailerMessage = $mailerMessage;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    protected function setParameters()
    {
        $this->parameters = null;
        $this->view = null;

        return $this;
    }

    /**
     * @throws \Throwable
     */
    protected function setEmailBody()
    {
        $this->body = $this->twig->render($this->view, $this->parameters);

        return $this;
    }

    protected function setMessage()
    {
        $this->mailerMessage = $this->mailerMessage->createMessage()
            ->setSubject($this->subject)
            ->setFrom($this->mailerUser)
            ->setTo($this->user->getEmail())
            ->setBody($this->body,'text/html');

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setAddressee(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function sendMail() :void
    {
        $this
            ->setParameters()
            ->setEmailBody()
            ->setMessage();

        $this->mailerTransport->send($this->mailerMessage);
    }
}