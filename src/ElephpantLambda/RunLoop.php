<?php

namespace ElephpantLambda;

use GuzzleHttp\Client as GuzzleClient;

class RunLoop
{
    /**
     * @todo Shall we add the task name to the param list, or pass ConfigStore?
     * @param GuzzleClient $client
     * @param string $runtimeHost
     */
    public function __construct(protected GuzzleClient $client, protected string $runtimeHost)
    {
    }

    /**
     * Runs an infinite processing loop (until the environment is shut down)
     *
     * @todo Need to fix the handlerFunction - let's just call a global in the short term
     * i.e. \myFunction();
     */
    public function runLoop(): void
    {
        do {
            // Ask the runtime API for a request to handle
            $request = $this->getNextRequest();

            // Execute the desired function and obtain the response
            $response = $handlerFunction($request['payload']);

            // Submit the response back to the runtime API
            $this->sendResponse($request['invocationId'], $response);
        } while (true);

    }

    protected function getNextRequest(): array
    {
        // Use $this->getRuntimeHost()
        return [];
    }

    /**
     * @todo What is the type of the invocationId? - string?
     * @param $invocationId
     * @param string $response
     */
    protected function sendResponse($invocationId, string $response): void
    {
        // Use $this->getRuntimeHost()
    }

    protected function getRuntimeHost(): string
    {
        return $this->runtimeHost;
    }

    protected function getTaskName(): string
    {
        return ''; // FIXME
    }
}
