<?php

namespace App\Enums;

enum ContentMode: string
{
    case Video = 'video';
    case Live = 'live';
    case Mixed = 'mixed';
}
