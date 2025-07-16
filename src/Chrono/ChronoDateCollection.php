<?php

namespace SamBakon\Chrono\Chrono;

use DateTimeInterface;

/**
 * Handles date collections
 */
class ChronoDateCollection
{
    /**
     * Array of dates
     * @var DateTimeInterface[]
     */
    public $dates;

    public function __construct(array $dates = [])
    {
        $this->dates = $dates;
    }
}
