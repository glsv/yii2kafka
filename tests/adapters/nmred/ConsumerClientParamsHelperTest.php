<?php

use PHPUnit\Framework\TestCase;
use yii2Kafka\adapters\nmred\ConsumerClientParamsHelper;
use yii2Kafka\exceptions\InvalidConfigException;

class ConsumerClientParamsHelperTest extends TestCase
{
    public function testFailEmptyParams()
    {
        $this->expectException(InvalidConfigException::class);
        $params = [];
        ConsumerClientParamsHelper::validateParams($params);
    }

    public function testFailMissingTopics()
    {
        $this->expectExceptionMessageMatches('/consumer\[topics\]/i');
        $params = [
            'groupId' => 'group'
        ];
        ConsumerClientParamsHelper::validateParams($params);
    }

    public function testFailTopicString()
    {
        $this->expectExceptionMessageMatches('/consumer\[topics\].*array/i');
        $params = [
            'topics' => 'topic'
        ];
        ConsumerClientParamsHelper::validateParams($params);
    }

    public function testFailMissingGroupId()
    {
        $this->expectExceptionMessageMatches('/consumer\[groupId\]/i');
        $params = [
            'topics' => ['topic']
        ];
        ConsumerClientParamsHelper::validateParams($params);
    }

    public function testSuccess()
    {
        $this->expectNotToPerformAssertions();

        $params = [
            'topics' => ['topic'],
            'groupId' => 'group'
        ];
        ConsumerClientParamsHelper::validateParams($params);
    }
}