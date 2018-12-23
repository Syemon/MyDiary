<?php

namespace AppBundle\Service;

class EmailMessageFactory
{
    const TYPE_REGISTRATION = 0;
    /**
     * @var RegistrationEmail
     */
    private $registrationEmail;

    public function __construct(RegistrationEmail $registrationEmail)
    {
        $this->registrationEmail = $registrationEmail;
    }

    public function getMessenger($messengerType)
    {
        switch ($messengerType) {
            case self::TYPE_REGISTRATION:
                return $this->registrationEmail;
        }
    }
}