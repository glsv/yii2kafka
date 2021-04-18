<?php

namespace yii2Kafka\interfaces;

interface KafkaConsumerInterface
{
    public function consume(\Closure $handler);
    public function getClient();
}