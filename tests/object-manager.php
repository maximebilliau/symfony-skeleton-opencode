<?php

declare(strict_types=1);

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$appEnv = $_ENV['APP_ENV'] ?? 'test';
$appDebug = (bool) ($_ENV['APP_DEBUG'] ?? false);

if (!is_string($appEnv)) {
    $appEnv = 'test';
}

$kernel = new Kernel($appEnv, $appDebug);
$kernel->boot();

return $kernel->getContainer()->get(EntityManagerInterface::class);
