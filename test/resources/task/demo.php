<?php

function demo(array $data): string
{
    return APIResponse("Hello, " . $data['name']);
}

function APIResponse(string $body): string
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
