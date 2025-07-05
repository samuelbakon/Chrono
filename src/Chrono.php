<?php

namespace SamyAsm\Chrono;

use SamyAsm\Chrono\Traits\ChronoCalendarTrait;
use SamyAsm\Chrono\Traits\ChronoComputingTrait;
use SamyAsm\Chrono\Traits\ChronoPeriodTrait;
use SamyAsm\Chrono\Traits\ChronoCastingTrait;
use SamyAsm\Chrono\Traits\ChronoUtilsTrait;
use SamyAsm\Chrono\Traits\ChronoFactoryTrait;
use SamyAsm\Chrono\Traits\ChronoFormatTrait;

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
