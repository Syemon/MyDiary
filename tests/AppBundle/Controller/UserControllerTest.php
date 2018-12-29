<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadUserFixture;
use AppBundle\Entity\User;
use Tests\AbstractWebTestCase;

class UserControllerTest extends AbstractWebTestCase
{
    public function testRegisterAction()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Login')->link();
        $crawler = $client->click($link);

        $link = $crawler->selectLink('Register')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Register')->form();

        $crawler = $client->submit($form, array(
            'user_registration_form[nickname]' => 'nick',
            'user_registration_form[email]' => 'lorem.ipsum@example.com',
            'user_registration_form[phoneNumber]' => '111222333',
            'user_registration_form[plainPassword][first]' => 'qwerty123',
            'user_registration_form[plainPassword][second]' => 'qwerty123'
        ));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginAction()
    {
        $this->container = self::$kernel->getContainer();
        $translator = $this->container->get('translator');

        $fixture = new LoadUserFixture();
        $fixture->load($this->entityManager);
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findAll()[0];

        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Login')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Login')->form();

        $crawler = $client->submit($form, array(
            'login_form[_username]' => $user->getPhoneNumber(),
            'login_form[_password]' => $user->getPlainPassword(),
          ));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame($translator->trans('user_diary'), $crawler->filter('h1')->eq(1)->text());
    }
}
