<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.01.18
 * Time: 15:01
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testHomepageAction()
    {
        $client = static::createClient();

        $clawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertSame('MyDiary', $clawler->filter('h1')->text());
    }
}