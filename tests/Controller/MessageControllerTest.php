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
        $this->assertIsString($responseContent);
        $data = json_decode($responseContent, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        $this->assertCount(1, $data['messages']);

        $client->request('GET', '/messages', ['status' => 'delivered']);
        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $this->assertIsString($responseContent);
        $data = json_decode($responseContent, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        foreach ($data['messages'] as $message) {
            $this->assertEquals('delivered', $message['status']);
        }
    }


    public function testThatItSendsAMessage(): void
    {
        $client = static::createClient();

        // Updated test to reflect the use of POST for message creation, ensuring the correct method is being tested.
        $client->request('POST', '/messages/send', [
            'text' => 'Hello World',
        ]);

        // Asserting that the response is successful (HTTP 201 for resource creation).
        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            // Asserting that the SendMessage command was correctly dispatched and is in the message queue.
            ->assertContains(SendMessage::class, 1);
    }
}
