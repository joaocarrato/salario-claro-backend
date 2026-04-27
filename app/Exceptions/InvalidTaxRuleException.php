<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class InvalidTaxRuleException extends RuntimeException
{
    public static function missing(string $type, int $year): self
    {
        return new self("Tax rule [{$type}] for year [{$year}] was not found.");
    }

    public static function invalid(string $type, int $year, string $reason): self
    {
        return new self("Tax rule [{$type}] for year [{$year}] is invalid: {$reason}");
    }
}
