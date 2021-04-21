<?php

namespace yii2Kafka\adapters\longlang;

use yii2Kafka\ConsumerConfig;
use yii2Kafka\exceptions\InvalidConfigException;

class ConsumerClientParamsHelper
{
    public static function prepareParams(array $clientParams, ConsumerConfig $config): array
    {
        $params = [];
        $params['topic'] =  $config->getTopics();
        $params['clientId'] =  $config->getClientId();
        $params['groupId'] =  $config->getGroupId();
        $params['autoCommit'] =  $config->isAutoCommit();

        $params = array_merge($clientParams, array_filter($params));
        self::validateParams($params);

        return $params;
    }

    public static function validateParams(array $params): void
    {
        if (!isset($params['topic'])) {
            throw new InvalidConfigException('consumer[topic] must be set in the params');
        }

        if (!isset($params['clientId'])) {
            throw new InvalidConfigException('consumer[client_id] must set in params');
        }

        if (!isset($params['groupId'])) {
            throw new InvalidConfigException('consumer[group_id] must set in params');
        }
    }
}