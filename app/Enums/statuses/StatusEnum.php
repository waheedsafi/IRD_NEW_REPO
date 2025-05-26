<?php

namespace App\Enums\Statuses;

enum StatusEnum: int
{
    case active = 1;
    case blocked = 2;
    case register_form_not_completed = 3;
    case register_form_completed = 4;
    case signed_register_form_submitted = 5;
    case registered = 6;
    case registration_expired = 7;
    case registration_extended = 8;
}
