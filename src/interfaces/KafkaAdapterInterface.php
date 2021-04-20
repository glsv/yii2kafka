<?php

namespace yii2Kafka\interfaces;

use Psr\Log\LoggerInterface;
use yii2Kafka\Config;
use yii2Kafka\ConsumerConfig;

interface KafkaAdapterInterface
{
    public function setConfig(Config $config): void;
    public function setLogger(LoggerInterface $logger): void;
    public function setDebugMode(bool $debugMode): void;
    public function getProducer(): KafkaProducerInterface;
    public function getConsumer(): KafkaConsumerInterface;
    public function createConsumer(ConsumerConfig $config): KafkaConsumerInterface;
}