<?php

use PHPUnit\Framework\TestCase;
use ElephpantLambda\TaskLoader;

class TaskLoaderTest extends TestCase
{
    public function testTaskLoader()
    {
        $loader = new TaskLoader($this->getTaskDir());
        $loader->load('demo');
        $json = demo(['name' => 'Wily Coyote', ]);
        $result = json_decode($json, true);
        $this->assertEquals(
            'Hello, Wily Coyote',
            $result['body']
        );
    }

    protected function getTaskDir(): string
    {
        $testDir = realpath(__DIR__ . '/..');

        return $testDir . '/resources/task';
    }
}
