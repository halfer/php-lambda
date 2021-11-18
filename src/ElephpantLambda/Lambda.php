<?php

namespace ElephpantLambda;

abstract class Lambda
{
    abstract function run(array $data): string;

    protected function apiResponse(string $body): string
    {
        $headers = [
            "Content-Type" => "application/json",
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Allow-Headers" => "Content-Type",
            "Access-Control-Allow-Methods" => "OPTIONS,POST"
        ];
        return json_encode([
            "statusCode" => 200,
            "headers" => $headers,
            "body" => $body
        ]);
    }
}
