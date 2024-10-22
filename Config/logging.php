<?php

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs';

return [
    'error_log_path' => $baseDir . DIRECTORY_SEPARATOR . 'error.log',
    'debug_log_path' => $baseDir . DIRECTORY_SEPARATOR . 'debug.log',
    'query_log_path' => $baseDir . DIRECTORY_SEPARATOR . 'query.log'
];