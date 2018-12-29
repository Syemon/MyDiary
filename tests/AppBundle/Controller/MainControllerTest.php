<?php

namespace Tests\AppBundle\Controller;

use Tests\AbstractWebTestCase;

class MainControllerTest extends AbstractWebTestCase
{
    public function testHomepageAction()
    {
        $client = static::createClient();

        $clawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertSame('MyDiary', $clawler->filter('h1')->text());
    }
}
