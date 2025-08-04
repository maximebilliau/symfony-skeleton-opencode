<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Symfony\Component\Uid\UuidV4;
use Webmozart\Assert\Assert;

trait IdentifierTrait
{
    private function __construct(private readonly string $id)
    {
        Assert::uuid($id);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function toUuidV4(): UuidV4
    {
        return new UuidV4($this->toString());
    }
}
