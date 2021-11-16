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

    /**
     * @dataProvider keyDataProvider
     */
    public function testMissingValues(string $removeKey)
    {
        // Remove exactly one key from the example set
        $exampleEnv = $this->getExampleEnv();
        unset($exampleEnv[$removeKey]);

        // All run() calls should throw an exception
        $reader = $this->createEnvReaderInstance();
        $message = null;
        try {
            $reader->run($exampleEnv, $this->createStoreInstance());
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        // Check that an exception was thrown (and the message is correct)
        $this->assertEquals("Key `$removeKey` not found", $message);
    }

    public function keyDataProvider()
    {
        return [
            ['_HANDLER'],
            ['LAMBDA_TASK_ROOT'],
            ['AWS_LAMBDA_RUNTIME_API'],
        ];
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