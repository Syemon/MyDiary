<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use Tests\AbstractWebTestCase;

class DiaryTest extends AbstractWebTestCase
{
    /** @var Diary $diary */
    private $diary;

    public function setUp()
    {
        parent::setUp();
        $this->diary = new Diary();
    }

    public function testNoteAttribute()
    {
        $this->diary->setNote('Some note');
        $this->assertSame('Some note', $this->diary->getNote());
    }

    public function testAttachmentAttribute()
    {
        $this->diary->setAttachment('file.png');
        $this->assertSame('file.png', $this->diary->getAttachment());
    }

    public function testTitleAttribute()
    {
        $this->diary->setTitle('Title');
        $this->assertSame('Title', $this->diary->getTitle());
    }

    public function testCreatedAtAttribute()
    {
        $this->diary->setCreatedAtValue();
        $this->assertTrue($this->diary->getCreatedAt() instanceof \DateTime);
    }

    public function testUserAttribute()
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['getId'])
            ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->diary->setUser($user);
        $this->assertSame(1, $this->diary->getUser()->getId());
    }
}
