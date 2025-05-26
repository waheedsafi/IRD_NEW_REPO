<?php

namespace App\Enums\Type;

enum StatusTypeEnum: int
{
    case ngo = 1;
    case agreement = 2;
    case donor = 3;
    case project = 4;
    case general = 5;
}
