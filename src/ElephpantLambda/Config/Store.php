<?php

namespace ElephpantLambda\Config;

class Store
{
    protected string $taskName;
    protected string $taskPath;
    protected string $lambdaRuntimeApi;

    public function setTaskName(string $taskName)
    {
        $this->taskName = $taskName;

        return $this;
    }

    public function getTaskName(): string
    {
        $this->checkNotSet('taskName', 'Task name must be set first');
        return $this->taskName;
    }

    public function setTaskPath(string $taskPath)
    {
        $this->taskPath = $taskPath;

        return $this;
    }

    public function getTaskPath(): string
    {
        $this->checkNotSet('taskPath', 'Task path must be set first');
        return $this->taskPath;
    }

    /**
     * @todo Not sure the purpose of this method is clear
     */
    public function setLambdaRuntimeApi(string $lambdaRuntimeApi)
    {
        $this->lambdaRuntimeApi = $lambdaRuntimeApi;

        return $this;
    }

    public function getLambdaRuntimeApi(): string
    {
        $this->checkNotSet('lambdaRuntimeApi', 'Lambda runtime API must be set first');
        return $this->lambdaRuntimeApi;
    }

    protected function checkNotSet(string $property, string $message)
    {
        if (!isset($this->$property)) {
            throw new \RuntimeException($message);
        }
    }
}
