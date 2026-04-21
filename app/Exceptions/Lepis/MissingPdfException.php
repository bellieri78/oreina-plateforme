<?php

namespace App\Exceptions\Lepis;

use Exception;

class MissingPdfException extends Exception
{
    public static function forBulletin(int $bulletinId): self
    {
        return new self(
            "Le bulletin #{$bulletinId} n'a pas de PDF attaché — upload requis avant publication."
        );
    }
}
