<?php

namespace yii2Kafka\adapters\nmred;

use yii2Kafka\exceptions\InvalidConfigException;
use yii2Kafka\ConsumerConfig;

class ConsumerClientParamsHelper
{
    public static function prepareParams(array $globalConfig, array $consumerConfig, ?ConsumerConfig $config = null): array
    {
        $params = [];

        if ($config !== null) {
            $params['topics'] =  $config->getTopics();
            $params['clientId'] =  $config->getClientId();
            $params['groupId'] =  $config->getGroupId();
        }

        $params = array_merge($globalConfig, $consumerConfig, array_filter($params));
        self::validateParams($params);

        return $params;
    }

    public static function validateParams(array $params): void
    {
        if (!isset($params['topics']) || empty($params['topics'])) {
            throw new InvalidConfigException('consumer[topics] must be set for consumer params');
        }

        if (is_string($params['topics'])) {
            throw new InvalidConfigException('consumer[topics] param must be an array');
        }

        if (!isset($params['groupId']) || empty($params['groupId'])) {
            throw new InvalidConfigException('consumer[groupId] must be set for consumer params');
        }
    }
}