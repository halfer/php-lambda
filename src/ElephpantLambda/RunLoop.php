<?php

namespace ElephpantLambda;

use GuzzleHttp\Client as GuzzleClient;

class RunLoop
{
    /**
     * @param GuzzleClient $client
     * @param string $runtimeHost
     * @param string $taskName
     */
    public function __construct(protected GuzzleClient $client,
                                protected string $runtimeHost,
                                protected string $taskName)
    {
    }

    /**
     * Runs an infinite processing loop (until the environment is shut down)
     */
    public function runLoop(): void
    {
        do {
            // Ask the runtime API for a request to handle
            $request = $this->getNextRequest();

            // Execute the desired function and obtain the response
            $handlerFunction = $this->getTaskName();
            $response = $handlerFunction($request['payload']);

            // Submit the response back to the runtime API
            $this->sendResponse($request['invocationId'], $response);
        } while (true);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getNextRequest(): array
    {
        $response = $this
            ->getClient()
            ->get($this->getRuntimeBaseUrl() . '/next');

        return [
            'invocationId' => $response->getHeader('Lambda-Runtime-Aws-Request-Id')[0],
            'payload' => json_decode((string) $response->getBody(), true)
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
