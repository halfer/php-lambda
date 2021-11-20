<?php

use PHPUnit\Framework\TestCase;
use ElephpantLambda\RunLoop;
use ElephpantLambda\Exception\PayloadNotJson as PayloadNotJsonException;
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

    public function testRunLoopOnce()
    {
        // Here is our pretend lambda
        function index($data)
        {
            return 'hello';
        }

        $runLoop = $this->getRunLoopInstance('localhost', 'index');
        $this->setFetchInvocationIdExpectation();
        $this->setFetchBodyExpectation();
        $this->setFetchWorkExpectation();
        $this->setSendResultsExpectation();
        $runLoop->runLoop();
        $this->assertTrue(true);
    }

    public function testRunLoopFunctionNotInScope()
    {
        $this->markTestIncomplete();
    }

    public function testRunLoopMissingPayload()
    {
        $this->markTestIncomplete();
    }

    public function testRunLoopPayloadNotInJson()
    {
        $runLoop = $this->getRunLoopInstance('localhost', 'index');
        $this->setFetchInvocationIdExpectation();
        $this->setFetchBodyExpectation('hello');
        $this->setFetchWorkExpectation();
        $this->setSendResultsExpectation();
        $this->expectException(PayloadNotJsonException::class);

        $runLoop->runLoop();
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

    protected function setFetchWorkExpectation()
    {
        $this
            ->getGuzzleMock()
            ->shouldReceive('get')
            ->once()
            ->with('http://localhost/2018-06-01/runtime/invocation/next')
            ->andReturn($this->getResponseMock());
    }

    protected function setSendResultsExpectation()
    {
        $this
            ->getGuzzleMock()
            ->shouldReceive('post')
            ->once()
            ->with(
                'http://localhost/2018-06-01/runtime/invocation/123/response',
                ['body' => index('rah'),]
            );
    }

    protected function setFetchInvocationIdExpectation()
    {
        $this
            ->getResponseMock()
            ->shouldReceive('getHeader')
            ->once()
            ->with('Lambda-Runtime-Aws-Request-Id')
            ->andReturn(['123']);
    }

    protected function setFetchBodyExpectation($body = null)
    {
        if (is_null($body)) {
            $body = json_encode([
                'payload' => 'hello',
                'invocationId' => '123',
            ]);
        }

        $streamMock = Mockery::mock(\GuzzleHttp\Psr7\Stream::class);
        $streamMock
            ->shouldReceive('__toString')
            ->once()
            ->andReturn($body);

        $this
            ->getResponseMock()
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($streamMock);
    }

    protected function getRunLoopInstance(string $runtimeHost, string $taskName): RunLoop
    {
        $runLoop = new RunLoop($this->getGuzzleMock(), $runtimeHost, $taskName);
        $runLoop->setInfiniteLoop(false);

        return $runLoop;
    }

    protected function createGuzzleMock(): GuzzleClient
    {
        return Mockery::mock(GuzzleClient::class);
    }

    protected function createResponseMock(): GuzzleHttp\Psr7\Response
    {
        return Mockery::mock(GuzzleHttp\Psr7\Response::class);
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
