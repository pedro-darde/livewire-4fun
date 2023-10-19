<?php

namespace App\Enums;

enum NotificationType: string
{
    case APPOINTMENT = 'A';
    case MEETING = 'M';
    case REMINDER = 'R';
}
