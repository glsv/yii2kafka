<?php

namespace yii2Kafka;

use yii2Kafka\exceptions\RuntimeException;
use yii2Kafka\traits\LoggerTrait;

abstract class BaseKafkaProducer
{
    use LoggerTrait;

    abstract protected function sendInternal(string $topicName, $value, $key = ''): void;

    /**
     * @param Message[] $messages
     */
    abstract protected function sendBatchInternal(array $messages): void;

    public function send(string $topicName, $value, $key = '')
    {
        $this->debug('Send message: ' . json_encode(['topic' => $topicName, 'value' => $value]));

        try {
            $this->sendInternal($topicName, $value, $key);
        } catch (\Exception $exc) {
            throw new RuntimeException(sprintf("Error send via client %s", static::class), 0, $exc);
        }
    }

    /**
     * @param Message[] $messages
     */
    public function sendBatch(array $messages): void
    {
        if (empty($messages)) {
            return;
        }

        $this->debug('Send batch messages: ' . json_encode($messages));

        try {
            $this->sendBatchInternal($messages);
        } catch (\Exception $exc) {
            throw new RuntimeException(sprintf("Error sendBatch via client %s", static::class), 0, $exc);
        }
    }
}