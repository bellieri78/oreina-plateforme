<?php

namespace App\Services;

final class SnapshotResult
{
    public function __construct(
        public readonly int $paperCount,
        public readonly int $digitalCount,
        public readonly array $skipped
    ) {}
}
