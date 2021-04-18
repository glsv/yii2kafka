<?php

namespace yii2Kafka;

use yii2Kafka\exceptions\DomainException;

class Message
{
    private $topicName;
    private $value;
    private $key = '';

    public function __construct(string $topicName, $value)
    {
        if ($topicName === "") {
            throw new DomainException('topicName is empty');
        }

        $this->topicName = $topicName;
        $this->value = $value;
    }

    public function topicName(): string
    {
        return $this->topicName;
    }

    public function value()
    {
        return $this->value;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }

    public function key(): string
    {
        return $this->key;
    }
}