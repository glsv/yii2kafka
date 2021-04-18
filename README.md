# Интеграция клиентов для Kafka в Yii2

Install
---
```php composer require --prefer-dist glsv/yii2kafka```

Supported Clients
---
- [longyan/phpkafka](https://github.com/longyan/phpkafka)
- [weiboad/kafka-php](https://github.com/weiboad/kafka-php)

## Integration client longyan/phpkafka
```php composer require --prefer-dist longlang/phpkafka```

### Config yii2
```
components = [
    'class' => \yii2Kafka\YiiKafkaComponent::class,
    // https://github.com/longyan/phpkafka
    'adapter' => [
        'class' => \yii2Kafka\adapters\longlang\LonglangAdapter::class,
        // Params for producer client
        'params' => [
            // https://github.com/longyan/phpkafka/blob/master/doc/producer.en.md
            'producer' => [
                'autoCreateTopic' => false,
                'acks' => 1,
            ],
            // https://github.com/longyan/phpkafka/blob/master/doc/consumer.en.md
            'consumer' => [
                'topic' => ['test-topic'],
                'groupId' => 'consumer-group',
                'clientId' => 'client-consumer',
                'autoCommit' => true,
            ]
        ],
    ],
    'loggerFactory' => function () {
        return \Yii::$container->get(\yii2Kafka\interfaces\KafkaLoggerInterface::class);
    },
    'brokers' => ['kafka:9092']
]
```
