<?php

use PHPUnit\Framework\TestCase;
use yii2Kafka\adapters\nmred\NmredAdapter;
use yii2Kafka\adapters\nmred\NmredConsumer;
use yii2Kafka\adapters\nmred\NmredProducer;

class AdapterTest extends TestCase
{
    public function testCreateAdapter(): NmredAdapter
    {
        $params = [
            'global' => [],
            'producer' => [
                'isAsyn' => false,
                'requiredAck' => 1,
            ],
            'consumer' => [
                'topics' => ['text-topic'],
                'groupId' => 'group_1'
            ]
        ];

        $config = new \yii2Kafka\Config();
        $config->setBrokers(['kafka:9092']);

        $adapter = new NmredAdapter($config);
        $adapter->params = $params;

        self::assertInstanceOf(NmredAdapter::class, $adapter);

        return $adapter;
    }

    /**
     * @depends testCreateAdapter
     * @param NmredAdapter $adapter
     */
    public function testGetConsumer(NmredAdapter $adapter)
    {
        $consumer = $adapter->getConsumer();
        self::assertInstanceOf(NmredConsumer::class, $consumer);
    }

    /**
     * @depends testCreateAdapter
     * @param NmredAdapter $adapter
     */
    public function testGetProducer(NmredAdapter $adapter)
    {
        $clientProducer = $this->createMock(\Kafka\Producer::class);
        $reflectionAdapter = new ReflectionClass($adapter);
        $property = $reflectionAdapter->getProperty('clientProducer');
        $property->setAccessible(true);
        $property->setValue($clientProducer);

        $producer = $adapter->getProducer();
        self::assertInstanceOf(NmredProducer::class, $producer);
    }

    /**
     * @depends testCreateAdapter
     * @param NmredAdapter $adapter
     */
    public function testCreateConsumer(NmredAdapter $adapter)
    {
        $config = new \yii2Kafka\ConsumerConfig("test-topic");

        $consumer = $adapter->createConsumer($config);
        self::assertInstanceOf(NmredConsumer::class, $consumer);
    }
}