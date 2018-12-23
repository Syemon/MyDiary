<?php

namespace AppBundle\Service;


class RegistrationEmail extends AbstractEmailMessage
{

    protected $view = 'emails/registration.html.twig';
    protected $subject = 'Account activation';

    protected function setParameters()
    {
        $user = $this->user;
        $confirmationToken = $user->getConfirmationToken();

        $this->parameters = [
            'user' => $user,
            'confirmationLink' => sprintf('%s%s','127.0.0.1:8000/activate/', $confirmationToken)
        ];

        return $this;
    }
}