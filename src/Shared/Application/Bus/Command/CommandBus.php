<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\Command;

use Symfony\Component\Messenger\Envelope;

interface CommandBus
{
    public function dispatch(object $command): Envelope;
}
