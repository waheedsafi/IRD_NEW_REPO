<?php

namespace App\Enums;

enum RoleEnum: int
{
    case super = 1;
    case admin = 2;
    case user = 3;
    case debugger = 4;
    case ngo = 5;
    case donor = 6;
}
