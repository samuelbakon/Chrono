<?php

namespace SamyAsm\Chrono\Chrono;

use DateInterval;
use DateTimeInterface;
use Exception;

/**
 * Handles date intervals
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
