<?php

namespace yii2Kafka\interfaces;

use Psr\Log\LoggerInterface;

interface KafkaLoggerInterface
{
    public function getLogger(): LoggerInterface;
}