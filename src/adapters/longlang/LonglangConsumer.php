<?php

namespace yii2Kafka\adapters\longlang;

use longlang\phpkafka\Consumer\OffsetManager;
use yii2Kafka\BaseKafkaConsumer;
use yii2Kafka\interfaces\KafkaConsumerInterface;
use longlang\phpkafka\Consumer\Consumer;
use yii2Kafka\Message;

class LonglangConsumer extends BaseKafkaConsumer implements KafkaConsumerInterface
{
    /**
     * @var Consumer
     */
    protected $consumer;

    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    protected function consumeInternal(\Closure $handler): void
    {
        $config = $this->consumer->getConfig();
        $interval = (int) ($config->getInterval() * 1000000);
        $autoCommit = $config->getAutoCommit();

        while (true) {
            $message = $this->consumer->consume();
            if ($message) {
                $mess = new Message($message->getTopic(), $message->getValue());
                $mess->setKey($message->getKey() ?? '');

                $handler($mess);

                if ($autoCommit) {
                    $this->consumer->ack($message);
                }
            } else {
                usleep($interval);
            }
        }
    }

    public function getClient(): Consumer
    {
        return $this->consumer;
    }

    public function changeOffset(int $offset): void
    {
        $property = new \ReflectionProperty(OffsetManager::class, 'offsets');
        $property->setAccessible(true);

        foreach ($this->consumer->getConfig()->getTopic() as $topic) {
            $offsetManager = $this->consumer->getOffsetManager($topic);
            $offsets = $offsetManager->getOffsets();

            foreach ($offsetManager->getPartitions() as $partition) {
                $offsets[$partition] = $offset;
            }

            $property->setValue($offsetManager, $offsets);

            foreach ($offsetManager->getPartitions() as $partition) {
                $offsetManager->saveOffsets($partition);
            }
        }

        $property->setAccessible(false);
    }
}