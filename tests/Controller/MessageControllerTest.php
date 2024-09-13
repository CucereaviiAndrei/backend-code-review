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

        // Test case 1: Without status parameter, expect all messages.
        $client->request('GET', '/messages');
        $this->assertResponseIsSuccessful();

        // Retrieve the response data and decode the JSON
        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);

        // Assert the structure and presence of 'messages' key
        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        // Optionally assert the number of messages (depends on the fixture data)
        $this->assertCount(1, $data['messages']);

        // Test case 2: With a status parameter
        $client->request('GET', '/messages', ['status' => 'delivered']);
        $this->assertResponseIsSuccessful();

        // Check the filtered result
        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);

        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        // Assuming fixtures set at least one message with 'delivered' status
        foreach ($data['messages'] as $message) {
            $this->assertEquals('delivered', $message['status']);
        }
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
