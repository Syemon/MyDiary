<?php

namespace Tests\AppBundle\Service;

use AppBundle\DataFixtures\ORM\LoadUserFixture;
use AppBundle\Entity\User;
use AppBundle\Service\RegistrationEmail;
use Swift_Message;
use Swift_Plugins_MessageLogger;
use Tests\AbstractWebTestCase;

class RegistrationEmailTest extends AbstractWebTestCase
{
    /** @var RegistrationEmail $registrationEmail */
    private $registrationEmail;
    /** @var Swift_Plugins_MessageLogger */
    private $logger;

    public function setUp()
    {
        parent::setUp();
        $this->container = self::$kernel->getContainer();
        $this->registrationEmail = $this->container->get('test_AppBundle\Service\RegistrationEmail');
        $mailer = $this->container->get('mailer');
        $this->logger = new Swift_Plugins_MessageLogger();
        $mailer->registerPlugin($this->logger);
    }

    /**
     * @throws \Throwable
     */
    public function testSendMail()
    {
        $fixture = new LoadUserFixture();
        $fixture->load($this->entityManager);

        $parameterSender = $this->container->getParameter('mailer_user');
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findAll()[0];
        $emailSubject = 'Account activation';

        $this->registrationEmail->setAddressee($user);
        $this->registrationEmail->sendMail();

        $emailMessage = $this->logger->getMessages()[0];

        $mailerAddressee = $this->getObjectKey($emailMessage->getTo());
        $mailerSender = $this->getObjectKey($emailMessage->getFrom());
        $mailerSubject = $emailMessage->getSubject();
        $mailerBody = $emailMessage->getBody();

        $this->assertInstanceOf(Swift_Message::class, $emailMessage);
        $this->assertSame($user->getEmail(), $mailerAddressee);
        $this->assertSame($parameterSender, $mailerSender);
        $this->assertSame($emailSubject, $mailerSubject);
        $this->assertNotNull($mailerBody);
    }

    /**
     * @param $object
     * @return int|mixed|string
     */
    private function getObjectKey($object)
    {
        foreach ($object as $key => $value) {
            $extractedKey = $key;
        }
        /** @var mixed $extractedKey */
        return $extractedKey;
    }
}