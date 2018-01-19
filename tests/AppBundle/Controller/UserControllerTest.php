<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    public function testRegisterAction()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        // Crawler goes to the login page
        $link = $crawler->selectLink('Login')->link();
        $crawler = $client->click($link);

        // Crawler goes to the registration page
        $link = $crawler->selectLink('Register')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Register')->form();

        $crawler = $client->submit($form, array(
            'user_registration_form[nickname]' => 'nick',
            'user_registration_form[email]' => 'szymo1990@gmail.com',
            'user_registration_form[phoneNumber]' => '111',
            'user_registration_form[plainPassword][first]' => '1',
            'user_registration_form[plainPassword][second]' => '1'

        ));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}