<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Diary;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;

class UserTest extends WebTestCase
{
    public function testUserGettersAndSetters()
    {
        $token = bin2hex(random_bytes(10));
        $user = new User();

        $user->setEmail('test@gmail.com');
        $user->setNickname('Nick');
        $user->setPhoneNumber('123456789');
        $user->setPlainPassword('qwerty');
        $user->setIsActive(true);
        $user->setConfirmationToken($token);

        $this->assertSame('test@gmail.com', $user->getEmail());
        $this->assertSame('Nick', $user->getNickname());
        $this->assertSame('123456789', $user->getPhoneNumber());
        $this->assertSame('qwerty', $user->getPlainPassword());
        $this->assertSame(true, $user->getIsActive());
        $this->assertSame($token, $user->getConfirmationToken());
    }

    public function testDiaryGettersAndSetters()
    {
        $diary = new Diary();
        $currentDate = date_create('now');

        $diary->setNote('Some note');
        $diary->setAttachment('file.png');
        $diary->setTitle('Title');
        $diary->setCreatedAtValue();

        $this->assertSame('file.png', $diary->getAttachment());
        $this->assertSame('Some note', $diary->getNote());
        $this->assertSame('Title', $diary->getTitle());
        $this->assertNotSame($currentDate, $diary->getCreatedAt());

    }
    public function testIsActiveDefaultValue()
    {
        $user = new User();

        $this->assertSame(false, $user->getIsActive());

    }
}
