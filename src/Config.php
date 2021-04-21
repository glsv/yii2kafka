<?php

namespace yii2Kafka;

class Config
{
    private $brokers = [];

    public function addBroker(string $broker): void
    {
        $this->brokers[] = $broker;
    }

    public function setBrokers(array $brokers): void
    {
        $this->brokers = $brokers;
    }

    public function brokers(): array
    {
        return $this->brokers;
    }

    public function brokerListForKafka(): string
    {
        return implode(',' , $this->brokers);
    }
}