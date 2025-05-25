<?php

namespace App\Enums\Type;

enum StatusTypeEnum: int
{
    case ngo_status = 1;
    case agreement_status = 2;
    case donor_status = 3;
    case project_status = 4;
    case general = 5;
}
