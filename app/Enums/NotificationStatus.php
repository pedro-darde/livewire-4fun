<?php

namespace App\Enums;

enum NotificationStatus: int
{
    case PENDING = 1;
    case SENT = 2;
    case FAILED = 3;
}
