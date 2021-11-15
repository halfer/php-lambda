<?php

use PHPUnit\Framework\TestCase;

class LambdaConfigTest extends TestCase
{
    public function testSetTaskName()
    {
        $instance = $this->createLambdaConfigInstance();
        $instance->setTaskName('cron');
        $this->assertEquals('cron', $instance->getTaskName());
    }

    public function testSetTaskPath()
    {
        $instance = $this->createLambdaConfigInstance();
        $instance->setTaskPath('/var/task');
        $this->assertEquals('/var/task', $instance->getTaskPath());
    }

    public function testSetLambdaRuntimeApi()
    {
        $instance = $this->createLambdaConfigInstance();
        $instance->setLambdaRuntimeApi('localhost');
        $this->assertEquals('localhost', $instance->getLambdaRuntimeApi());
    }

    public function testTaskNameMustBeSetFirst()
    {
        $message = null;
        try {
            $this->createLambdaConfigInstance()->getTaskName();
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Task name must be set first', $message);
    }

    public function testTaskPathMustBeSetFirst()
    {
        $message = null;
        try {
            $this->createLambdaConfigInstance()->getTaskPath();
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Task path must be set first', $message);
    }

    public function testLambdaRuntimeApiMustBeSetFirst()
    {
        $message = null;
        try {
            $this->createLambdaConfigInstance()->getLambdaRuntimeApi();
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Lambda runtime API must be set first', $message);
    }

    protected function createLambdaConfigInstance()
    {
        return new \ElephpantLambda\Config\Store();
    }
}
