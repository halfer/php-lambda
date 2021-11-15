<?php

namespace ElephpantLambda\Config;

class EnvReader
{
    public function run(array $env, Store $store): Store
    {
        $store
            ->setTaskName($this->getValue($env, '_HANDLER'))
            ->setTaskPath($this->getValue($env, 'LAMBDA_TASK_ROOT'))
            ->setLambdaRuntimeApi($this->getValue($env, 'AWS_LAMBDA_RUNTIME_API'));

        return $store;
    }

    protected function getValue(array $env, string $keyName): string
    {
        if (!isset($env[$keyName]))
        {
            throw new \RuntimeException(sprintf('Key `%s` not found'));
        }

        return (string) $env[$keyName];
    }
}
