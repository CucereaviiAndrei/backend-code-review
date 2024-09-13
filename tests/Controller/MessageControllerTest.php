<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function testList(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages');
        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);

        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        $this->assertCount(1, $data['messages']);

        $client->request('GET', '/messages', ['status' => 'delivered']);
        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);

        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        foreach ($data['messages'] as $message) {
            $this->assertEquals('delivered', $message['status']);
        }
    }


    public function testThatItSendsAMessage(): void
    {
        $client = static::createClient();

        $client->request('POST', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}
