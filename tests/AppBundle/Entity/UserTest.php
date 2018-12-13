<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Tests\AbstractWebTestCase;

class UserTest extends AbstractWebTestCase
{
    /** @var User $user */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testEmailAttribute()
    {
        $this->user->setEmail('john@example.com');
        $this->assertSame('john@example.com', $this->user->getEmail());
    }

    public function testPhoneNumberAttribute()
    {
        $this->user->setPhoneNumber('123123123');
        $this->assertSame('123123123', $this->user->getPhoneNumber());
    }

    public function testPasswordAttribute()
    {
        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());
    }

    public function testPlainPasswordAttribute()
    {
        $this->user->setPlainPassword('password');
        $this->assertSame('password', $this->user->getPlainPassword());
    }

    /**
     * @throws \Exception
     */
    public function testConfirmationTokenAttribute()
    {
        $token = bin2hex(random_bytes(10));

        $this->user->setConfirmationToken($token);
        $this->assertSame($token, $this->user->getConfirmationToken());
    }

    public function testIsActiveAttribute()
    {
        $this->user->setIsActive(true);
        $this->assertTrue($this->user->getIsActive());
    }

    public function testIsActiveAttributeDefaultValue()
    {
        $this->assertSame(false, $this->user->getIsActive());
    }
}
