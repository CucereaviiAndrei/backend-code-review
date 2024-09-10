<?php

declare(strict_types=1);

namespace Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function testList(): void
    {
        $this->markTestIncomplete('the Controller-Action needs tests');
    }

    public function testThatItSendsAMessage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}
