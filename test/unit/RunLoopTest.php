<?php

use PHPUnit\Framework\TestCase;
use ElephpantLambda\RunLoop;
use ElephpantLambda\Exception\PayloadNotJson as PayloadNotJsonException;
use ElephpantLambda\Exception\MissingInvocationIdHeader
    as MissingInvocationIdHeaderException;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

// Here is our pretend lambda
function index($data)
{
    return 'hello';
}

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
        $runLoop = $this->getRunLoopInstance('localhost', 'index');
        $this->setFetchInvocationIdExpectation(false);
        $this->setFetchBodyExpectation();
        $this->setFetchWorkExpectation();
        $this->setSendResultsExpectation();
        $this->expectException(MissingInvocationIdHeaderException::class);

        $runLoop->runLoop();
    }

    public function testGetRequestHttpFault()
    {
        $runLoop = $this->getRunLoopInstance('localhost', 'index');
        $this->setFetchInvocationIdExpectation(false);
        // FIXME this should fail, since getBody is not called
        $this->setFetchBodyExpectation('', 500);
        $this->setFetchWorkExpectation();
        $this->setSendResultsExpectation();
        $this->expectException(RuntimeException::class);

        $runLoop->runLoop();
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

    protected function setFetchInvocationIdExpectation($populated = true)
    {
        $result = $populated ? ['123'] : [];
        $this
            ->getResponseMock()
            ->shouldReceive('getHeader')
            ->once()
            ->with('Lambda-Runtime-Aws-Request-Id')
            ->andReturn($result );
    }

    protected function setFetchBodyExpectation($body = null, $responseCode = 200)
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
            ->andReturn($streamMock)
            ->shouldReceive('getStatusCode')
            ->once()
            ->andReturn($responseCode);
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
