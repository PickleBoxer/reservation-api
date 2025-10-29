<?php

declare(strict_types=1);

namespace App\Enums;

enum ResourceType: string
{
    case ROOM = 'room';
    case VEHICLE = 'vehicle';
    case EQUIPMENT = 'equipment';
    case SPACE = 'space';
}
