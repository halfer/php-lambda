<?php

use PHPUnit\Framework\TestCase;
use ElephpantLambda\RunLoop;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class RunLoopTest extends TestCase
{
    protected GuzzleClient $guzzleMock;
    protected ResponseInterface $responseMock;

    /**
     * Called before every test
     */
    public function setUp(): void
    {
        $this->guzzleMock = $this->createGuzzleMock();
        $this->responseMock = $this->createResponseMock();
    }

    /**
     * This test crashes due to a type clash - need to rethink
     */
    public function __testRunLoopOnce()
    {
        $runLoop = $this->getRunLoopInstance('localhost', 'index');
        $responseMock = $this
            ->getResponseMock()
            ->expects($this->once())
            ->method('getHeader')
            ->with('Lambda-Runtime-Aws-Request-Id')
            ->willReturn(['123']);
        //echo 'Response mock type:' . get_class($responseMock) . "\n";

        $this
            ->getGuzzleMock()
            ->expects($this->once())
            ->method('get')
            ->with('http://localhost/2018-06-01/runtime/invocation/next')
            ->willReturn($responseMock);
        $runLoop->runLoop();
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

    protected function getRunLoopInstance(string $runtimeHost, string $taskName): RunLoop
    {
        $runLoop = new RunLoop($this->getGuzzleMock(), $runtimeHost, $taskName);
        $runLoop->setInfiniteLoop(false);

        return $runLoop;
    }

    protected function createGuzzleMock(): GuzzleClient
    {
        return $this->createMock(GuzzleClient::class);
    }

    protected function createResponseMock(): GuzzleHttp\Psr7\Response
    {
        return $this->createMock(GuzzleHttp\Psr7\Response::class);
    }

    protected function getGuzzleMock(): GuzzleClient
    {
        return $this->guzzleMock;
    }

    protected function getResponseMock(): ResponseInterface
    {
        return $this->responseMock;
    }
}
