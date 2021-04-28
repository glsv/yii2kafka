<?php

use PHPUnit\Framework\TestCase;
use Kafka\Consumer;
use yii2Kafka\adapters\nmred\NmredConsumer;

class ConsumerTest extends TestCase
{
    public function testGetClient(): void
    {
        $consumer = new NmredConsumer($this->createMock(Consumer::class));
        $client = $consumer->getClient();
        self::assertInstanceOf(Consumer::class, $client);
    }

    public function testSend(): void
    {
        $this->expectNotToPerformAssertions();
        $consumer = new NmredConsumer($this->createMock(Consumer::class));
        $consumer->consume(function () {});
    }
}