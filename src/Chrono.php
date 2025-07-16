<?php

namespace SamBakon\Chrono;

use SamBakon\Chrono\Traits\ChronoCalendarTrait;
use SamBakon\Chrono\Traits\ChronoComputingTrait;
use SamBakon\Chrono\Traits\ChronoPeriodTrait;
use SamBakon\Chrono\Traits\ChronoCastingTrait;
use SamBakon\Chrono\Traits\ChronoUtilsTrait;
use SamBakon\Chrono\Traits\ChronoFactoryTrait;
use SamBakon\Chrono\Traits\ChronoFormatTrait;

class Chrono
{
    use ChronoCalendarTrait, 
        ChronoComputingTrait, 
        ChronoPeriodTrait, 
        ChronoCastingTrait,
        ChronoUtilsTrait,
        ChronoFactoryTrait,
        ChronoFormatTrait;

}
