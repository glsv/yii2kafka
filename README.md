# Client integration for Kafka in Yii2

Install
---
```php composer require glsv/yii2kafka```

Supported php-clients
---
- [longyan/phpkafka](https://github.com/longyan/phpkafka)
- [weiboad/kafka-php](https://github.com/weiboad/kafka-php)

Sequence of steps
---
1. Install ```glsv/yii2kafka```
2. Select and install some php-client 
   ([longyan/phpkafka](https://github.com/longyan/phpkafka) or 
   [weiboad/kafka-php](https://github.com/weiboad/kafka-php))
3. Configure extension and client   

## Configure glsv/yii2kafka
```
components = [
    'class' => \yii2Kafka\YiiKafkaComponent::class,
    'brokers' => ['kafka:9092'],
    'defaultLogPath' => '@runtime/logs/kafka.log',
    'debugMode' => true,
    'adapter => [
       // configure adapter for php-client... 
    ]
]
```

## Integration php-client longyan/phpkafka
```php composer require longlang/phpkafka```

https://github.com/longyan/phpkafka

#### Config yii2
```
components = [
    'class' => \yii2Kafka\YiiKafkaComponent::class,
    'brokers' => ['kafka:9092']
     /**
     * https://github.com/longyan/phpkafka
     */
    'adapter' => [
        'class' => \yii2Kafka\adapters\longlang\LonglangAdapter::class,
        'params' => [
            ////////
            // Use the params for the client at the links:
            // producer: https://github.com/longyan/phpkafka/blob/master/doc/producer.en.md
            // consumer: https://github.com/longyan/phpkafka/blob/master/doc/consumer.en.md
            ////////
            'producer' => [
                'autoCreateTopic' => false,
                'acks' => 1,
            ],
            'consumer' => [
                'topic' => ['test-topic'],
                'groupId' => 'consumer-group',
                'clientId' => 'client-consumer',
            ]
        ],
    ],
]
```

## Integration php-client nmred/kafka-php
```php composer require nmred/kafka-php```

https://github.com/weiboad/kafka-php

#### Config yii2 with nmred/kafka-php
```
components = [
    'class' => \yii2Kafka\YiiKafkaComponent::class,
    'brokers' => ['kafka:9092']
    /**
     * https://github.com/weiboad/kafka-php
     */
    'adapter' => [
        'class' => \yii2Kafka\adapters\nmred\NmredAdapter::class,
        'params' => [
            ////////
            // Use the params for the client at the link:
            // https://github.com/weiboad/kafka-php/blob/master/docs/Configure.md
            ////////       
            'global' => [],
            'producer' => [
                'isAsyn' => false,
                'requiredAck' => 1,
            ],
            'consumer' => [
                'topics' => ['test-topic'],
                'groupId' => 'consumer-group'
            ]
        ]
    ],
]
```

## Produce example

**Producing single message**
```
/**
 * @var YiiKafkaComponent $kafka
 */
$kafka = \Yii::$app->kafka;

$producer = $kafka->getProducer();
$topicName = "test-topic"
$producer->send($topicName, 'Message: ' . date('d.m H:i:s'), '');
```

**Producing messages in batches**
```
/**
 * @var YiiKafkaComponent $kafka
 */
$kafka = \Yii::$app->kafka;

$producer = $kafka->getProducer();
$topicName = "test-topic"
$messages = [
    new Message($topicName, 'Message 1: ' . date('d.m H:i:s')),
    new Message($topicName, 'Message 2: ' . date('d.m H:i:s')),
];

$producer->sendBatch($messages);
```

## Consume example
**Consuming messages from a topic from settings**
```
/**
 * @var YiiKafkaComponent $kafka
 */
$kafka = \Yii::$app->kafka;

$consumer = $kafka->getConsumer();
$consumer->consume(function ($message) {
    var_dump($message);
});
```

**Consuming messages from a topic from settings**
```
/**
 * @var YiiKafkaComponent $kafka
 */
$kafka = \Yii::$app->kafka;
$topicName = 'another-topic';

$config = new ConsumerConfig($topicName);
$config->setClientId('client_2');
$config->setGroupIp('group_2');

$consumer = $kafka->getAdapterClient()->createConsumer($config);
$consumer->consume(function ($message) {
    var_dump($message);
});
```