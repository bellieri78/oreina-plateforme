<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * Forces the use of the worktree's bootstrap/app.php rather than the one
     * inferred from the symlinked vendor directory (which would point to the
     * parent project).
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $this->traitsUsedByTest = array_flip(class_uses_recursive(static::class));

        $app = require dirname(__DIR__) . '/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
