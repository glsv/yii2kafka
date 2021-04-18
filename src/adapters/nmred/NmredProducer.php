<?php

namespace yii2Kafka\adapters\nmred;

use Kafka\Producer;
use yii2Kafka\BaseKafkaProducer;
use yii2Kafka\exceptions\RuntimeException;
use yii2Kafka\interfaces\KafkaProducerInterface;
use yii2Kafka\Message;

class NmredProducer extends BaseKafkaProducer implements KafkaProducerInterface
{
    /**
     * @var Producer
     */
    protected $producer;
    protected $topicNames;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function sendInternal(string $topicName, $value, $key = ''): void
    {
        if ($topicName === "") {
            throw new RuntimeException('topic name is empty');
        }

        $this->checkTopicInKafka($topicName);

        $this->producer->send([
            [
                'topic' => $topicName,
                'value' => $value,
                'key' => $key,
            ]
        ]);
    }

    /**
     * @param Message[] $messages
     */
    protected function sendBatchInternal(array $messages): void
    {
        $packet = [];
        foreach ($messages as $message) {
            $this->checkTopicInKafka($message->topicName());

            $packet[] = [
                'topic' => $message->topicName(),
                'value' => $message->value(),
                'key' => $message->key()
            ];
        }

        $this->producer->send($packet);
    }

    private function checkTopicInKafka(string $topicName)
    {
        if ($this->topicNames === null) {
            $broker = \Kafka\Broker::getInstance();
            $this->topicNames = array_keys($broker->getTopics());
        }

        if (!in_array($topicName, $this->topicNames)) {
            throw new RuntimeException(sprintf("Topic %s does not exist in kafka", $topicName));
        }
    }

    public function getClient(): Producer
    {
        return $this->producer;
    }
}