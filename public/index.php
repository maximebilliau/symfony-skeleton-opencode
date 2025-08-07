<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $appEnv = $context['APP_ENV'] ?? 'prod';
    $appDebug = $context['APP_DEBUG'] ?? false;
    
    if (!is_string($appEnv)) {
        $appEnv = 'prod';
    }
    
    return new Kernel($appEnv, (bool) $appDebug);
};
