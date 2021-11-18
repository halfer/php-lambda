<?php

use PHPUnit\Framework\TestCase;
use ElephpantLambda\RunLoop;

class RunLoopTest extends TestCase
{
    public function testRunLoopOnce()
    {
        $this->markTestIncomplete();
    }

    public function testRunLoopFunctionNotInScope()
    {
        $this->markTestIncomplete();
    }

    public function testRunLoopMissingPayload()
    {
        $this->markTestIncomplete();
    }

    public function testRunLoopMissingInvocationId()
    {
        $this->markTestIncomplete();
    }

    public function testGetRequestHttpFault()
    {
        $this->markTestIncomplete();
    }

    public function testSendResponseHttpFault()
    {
        $this->markTestIncomplete();
    }

    protected function getRunLoopInstance(string $runtimeHost, string $taskName)
    {
        $runLoop = new RunLoop($this->getGuzzleMock(), $runtimeHost, $taskName);
        $runLoop->setInfiniteLoop(false);

        return $runLoop;
    }

    protected function getGuzzleMock()
    {
        // TODO
    }
}
