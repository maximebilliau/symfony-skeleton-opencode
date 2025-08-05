<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

abstract class IdType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_object($value) && $value::class === $this->getIdentifierClass()) {
            return $value;
        }

        if (!\is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['string']);
        }

        try {
            /** @psalm-suppress MixedAssignment */
            $identifier = \call_user_func_array([$this->getIdentifierClass(), 'fromString'], [$value]);
        } catch (\UnexpectedValueException) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $identifier;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_object($value) && $value::class === $this->getIdentifierClass()) {
            /** @psalm-suppress MixedMethodCall */
            return $value->toString();
        }

        if (\is_string($value)) {
            return $value;
        }

        throw ConversionException::conversionFailed((string) $value, $this->getName());
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    abstract protected function getIdentifierClass(): string;
}
