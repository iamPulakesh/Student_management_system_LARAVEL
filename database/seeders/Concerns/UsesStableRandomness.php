<?php

/**
 * Seeded randomness, so demo data is the same on every run.
 */

namespace Database\Seeders\Concerns;

use Random\Engine\Mt19937;
use Random\Randomizer;

trait UsesStableRandomness
{
    /**
     * Get a randomizer whose sequence depends only on the given key.
     *
     * The demo data looks random, but every seed run produces exactly the
     * same records, so a live demo always behaves the same way.
     */
    protected function randomizerFor(string $key): Randomizer
    {
        return new Randomizer(new Mt19937(crc32($key)));
    }
}
