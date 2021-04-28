<?php

use PHPUnit\Framework\TestCase;
use Kafka\Producer;
use yii2Kafka\adapters\nmred\NmredProducer;

class ProducerTest extends TestCase
{
    public $topicName = 'test-topic';

    public function testGetClient(): void
    {
        $producer = new NmredProducer($this->createMock(Producer::class));
        $client = $producer->getClient();
        self::assertInstanceOf(Producer::class, $client);
    }

    public function testSend(): NmredProducer
    {
        $this->expectNotToPerformAssertions();

        $producer = new NmredProducer($this->createMock(Producer::class));

        $reflection = new ReflectionClass($producer);
        $property = $reflection->getProperty('topicNames');
        $property->setAccessible(true);
        $property->setValue($producer, [$this->topicName]);

        $producer->send($this->topicName, 'value');

        return $producer;
    }

    /**
     * @depends testSend
     */
    public function testUnknownTopic(NmredProducer $producer): void
    {
        $unknownTopic = 'unknown-topic';
        $this->expectExceptionMessage("Topic $unknownTopic does not exist in kafka");
        $producer->send($unknownTopic, 'value');
    }

    /**
     * @depends testSend
     */
    public function testSendBatch(NmredProducer $producer): void
    {
        $this->expectNotToPerformAssertions();

        $messages = [
            new \yii2Kafka\Message($this->topicName, 1),
            new \yii2Kafka\Message($this->topicName, 2),
        ];

        $producer->sendBatch($messages);
    }
}