<?php

namespace App\Enums;

enum ScheduleType: string
{
    case Once = 'once';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
}
