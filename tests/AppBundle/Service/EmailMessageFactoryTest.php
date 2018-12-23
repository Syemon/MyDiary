<?php

namespace Tests\AppBundle\Service;

use AppBundle\DataFixtures\ORM\LoadUserFixture;
use AppBundle\Entity\User;
use AppBundle\Service\EmailMessageFactory;
use AppBundle\Service\RegistrationEmail;
use Swift_Message;
use Swift_Plugins_MessageLogger;
use Tests\AbstractWebTestCase;

class EmailMessageFactoryTest extends AbstractWebTestCase
{
    /** @var EmailMessageFactory $emailMessageFactory */
    private $emailMessageFactory;
    /** @var Swift_Plugins_MessageLogger */
    private $logger;

    public function setUp()
    {
        parent::setUp();
        $this->container = self::$kernel->getContainer();
        $this->emailMessageFactory = $this->container->get('test_AppBundle\Service\EmailMessageFactory');
        $mailer = $this->container->get('mailer');
        $this->logger = new Swift_Plugins_MessageLogger();
        $mailer->registerPlugin($this->logger);
    }

    /**
     * @throws \Throwable
     */
    public function testGetMessengerTypeRegistration()
    {
        $registrationEmail = $this->emailMessageFactory
            ->getMessenger(EmailMessageFactory::TYPE_REGISTRATION);

        $this->assertInstanceOf(RegistrationEmail::class, $registrationEmail);
    }
}