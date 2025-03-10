<?php

namespace App\Enums\Type;

enum ApprovalTypeEnum: int
{
    case pending = 1;
    case approved = 2;
    case rejected = 3;
}
