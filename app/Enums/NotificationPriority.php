<?php

namespace App\Enums;

enum NotificationPriority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case URGENT = 4;
}
