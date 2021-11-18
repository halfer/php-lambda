<?php

namespace ElephpantLambda;

use GuzzleHttp\Client as GuzzleClient;

class RunLoop
{
    public function __construct(protected GuzzleClient $client, protected string $runtimeHost)
    {
    }

    public function runLoop(): void
    {

    }

    protected function getNextRequest(): array
    {
        return [];
    }

    /**
     * @todo What is the type of the invocationId? - string?
     * @param $invocationId
     * @param string $response
     */
    protected function sendResponse($invocationId, string $response): void
    {

    }
}
