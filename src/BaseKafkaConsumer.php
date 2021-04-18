<?php

namespace yii2Kafka;

use yii2Kafka\exceptions\RuntimeException;
use yii2Kafka\traits\LoggerTrait;

abstract class BaseKafkaConsumer
{
    use LoggerTrait;

    abstract protected function consumeInternal(\Closure $handler): void;

    public function consume(\Closure $handler)
    {
        $this->debug('start consume message');

        try {
            $this->consumeInternal($handler);
        } catch (\Exception $exc) {
            throw new RuntimeException(sprintf("Error consume via client %s", static::class), 0, $exc);
        }
    }
}