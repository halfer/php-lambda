<?php

namespace ElephpantLambda;

class TaskLoader
{
    public function __construct(protected string $taskPath)
    {
    }

    public function load(string $taskName)
    {
        require_once $this->taskPath . '/' . $taskName . '.php';
    }
}
