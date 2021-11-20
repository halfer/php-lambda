<?php

namespace ElephpantLambda;

use ElephpantLambda\Exception\PayloadNotJson;
use ElephpantLambda\Exception\MissingInvocationIdHeader;
use ElephpantLambda\Exception\TaskNotCallable;
use ElephpantLambda\Exception\HttpFailure;
use GuzzleHttp\Client as GuzzleClient;

class RunLoop
{
    protected bool $infiniteLoop = true;

    /**
     * @param GuzzleClient $client
     * @param string $runtimeHost
     * @param string $taskName
     */
    public function __construct(protected GuzzleClient $client,
                                protected string       $runtimeHost,
                                protected string       $taskName)
    {
    }

    /**
     * Runs an infinite processing loop (until the environment is shut down)
     */
    public function runLoop(): void
    {
        $handlerFunction = $this->getTaskName();
        $this->checkTaskCallable($handlerFunction);

        do {
            // Ask the runtime API for a request to handle
            $request = $this->getNextRequest();

            // Execute the desired function and obtain the response
            $response = $this->callTask($handlerFunction, $request['payload']);

            // Submit the response back to the runtime API
            $this->sendResponse($request['invocationId'], $response);
        } while ($this->infiniteLoop);
    }

    protected function checkTaskCallable(string $handlerFunction)
    {
        if (!is_callable($handlerFunction)) {
            throw new TaskNotCallable('Cannot find task function');
        }
    }

    /**
     * We don't want exceptions in the task to crash the loop
     *
     * @param string $handlerFunction
     * @param $payload
     * @return array|mixed
     */
    protected function callTask(string $handlerFunction, $payload)
    {
        try {
            $response = $handlerFunction($payload);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }

    /**
     * Fetches a payload from a lambda run request from AWS
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getNextRequest(): array
    {
        $response = $this
            ->getClient()
            ->get($this->getRuntimeBaseUrl() . '/next');

        // Check that we have a good response
        if ($response->getStatusCode() !== 200) {
            throw new HttpFailure('HTTP error when fetching task');
        }

        $payload = json_decode((string)$response->getBody(), true);

        // Verify the payload sent to AWS is in the expected format
        if (!is_array($payload)) {
            throw new PayloadNotJson('The payload is not JSON');
        }
        // Verify that AWS has supplied an invocation ID
        $ids = $response->getHeader('Lambda-Runtime-Aws-Request-Id');
        if (!$ids) {
            throw new MissingInvocationIdHeader('The invocation ID header is missing');
        }

        return [
            'invocationId' => $ids[0],
            'payload' => $payload,
        ];
    }

    /**
     * @param $invocationId
     * @param string $response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @todo What is the type of the invocationId? - string?
     */
    protected function sendResponse($invocationId, string $response): void
    {
        $this
            ->getClient()
            ->post(
                $this->getRuntimeBaseUrl() . '/' . $invocationId . '/response',
                ['body' => $response]
            );
    }

    /**
     * Determines whether to loop once or forever (mostly used for testing)
     *
     * @param bool $infiniteLoop
     */
    public function setInfiniteLoop(bool $infiniteLoop)
    {
        $this->infiniteLoop = $infiniteLoop;
    }

    protected function getRuntimeBaseUrl(): string
    {
        return 'http://' . $this->getRuntimeHost() . '/2018-06-01/runtime/invocation';
    }

    protected function getRuntimeHost(): string
    {
        return $this->runtimeHost;
    }

    protected function getTaskName(): string
    {
        return $this->taskName;
    }

    protected function getClient(): GuzzleClient
    {
        return $this->client;
    }
}
