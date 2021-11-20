<?php

// Let's just load items manually for now
$projectRoot = realpath(__DIR__ . '/..');
require_once $projectRoot . '/src/ElephpantLambda/Config/Store.php';
require_once $projectRoot . '/src/ElephpantLambda/Config/EnvReader.php';
require_once $projectRoot . '/src/ElephpantLambda/TaskLoader.php';
require_once $projectRoot . '/src/ElephpantLambda/RunLoop.php';
require_once $projectRoot . '/src/ElephpantLambda/Exception/PayloadNotJson.php';
require_once $projectRoot . '/src/ElephpantLambda/Exception/MissingInvocationIdHeader.php';
require_once $projectRoot . '/src/ElephpantLambda/Exception/TaskNotCallable.php';
require_once $projectRoot . '/src/ElephpantLambda/Exception/HttpFailure.php';
