<?php

namespace App\Enums;

enum CheckListTypeEnum: int
{
    case ngo_registeration = 1;
    case project_registeration = 2;
    case ngo_agreement_extend = 3;
    case project_extend = 4;
    case scheduling = 5;
}
