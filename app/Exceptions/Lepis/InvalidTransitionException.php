<?php

namespace App\Exceptions\Lepis;

use Exception;

class InvalidTransitionException extends Exception
{
    public static function from(string $currentStatus, string $attemptedStatus): self
    {
        return new self(
            "Transition invalide du statut '{$currentStatus}' vers '{$attemptedStatus}'."
        );
    }
}
