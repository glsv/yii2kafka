<?php

namespace yii2Kafka\interfaces;

use yii2Kafka\Message;

interface KafkaProducerInterface
{
    public function send(string $topicName, $value, $key = '');

    /**
     * @param Message[] $mesages
     */
    public function sendBatch(array $messages): void;

    public function getClient();
}