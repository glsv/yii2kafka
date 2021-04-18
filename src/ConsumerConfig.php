<?php

namespace yii2Kafka;

use yii2Kafka\exceptions\DomainException;

class ConsumerConfig
{
    protected $topics;
    protected $client_id;
    protected $group_id;
    protected $autoCommit = true;

    public function __construct($topic)
    {
        if (is_string($topic)) {
            $topic = [$topic];
        } elseif (!is_array($topic)) {
            throw new DomainException('topic must be array or string');
        }

        if (empty($topic)) {
            throw new DomainException('topic is empty');
        }

        $this->topics = $topic;
    }

    public function addTopic(string $topic): void
    {
        $this->topics[] = $topic;
    }

    public function getTopics(): array
    {
        return $this->topics;
    }

    public function setClientId(string $client_id): void
    {
        $this->client_id = $client_id;
    }

    public function getClientId(): ?string
    {
        return $this->client_id;
    }

    public function setGroupIp(string $group_id): void
    {
        $this->group_id = $group_id;
    }

    public function getGroupId(): ?string
    {
        return $this->group_id;
    }

    public function setAutocommit(bool $autoCommit): void
    {
        $this->autoCommit = $autoCommit;
    }

    public function isAutoCommit(): bool
    {
        return $this->autoCommit;
    }
}