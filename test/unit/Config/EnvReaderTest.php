<?php

use PHPUnit\Framework\TestCase;

class EnvReaderTest extends TestCase
{
    public function testSuccessfulStore()
    {
        $reader = $this->createEnvReaderInstance();
        $store = $reader->run(
            $this->getExampleEnv(),
            $this->createStoreInstance()
        );
        $this->assertEquals('task', $store->getTaskName());
        $this->assertEquals('/path/to/task', $store->getTaskPath());
        $this->assertEquals('localhost', $store->getLambdaRuntimeApi());
    }

    public function testMissingValues()
    {
        $this->markTestIncomplete();
    }

    protected function getExampleEnv()
    {
        return [
            '_HANDLER' => 'task',
            'LAMBDA_TASK_ROOT' => '/path/to/task',
            'AWS_LAMBDA_RUNTIME_API' => 'localhost',
        ];
    }

    protected function createStoreInstance()
    {
        return new \ElephpantLambda\Config\Store();
    }

    protected function createEnvReaderInstance()
    {
        return new \ElephpantLambda\Config\EnvReader();
    }
}