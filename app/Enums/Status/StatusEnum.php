<?php

namespace App\Enums\Status;

enum StatusEnum: int
{
    // General
    case active = 1;
    case block = 2;
    case document_upload_required = 3;
    case pending_approval = 4;
    case rejected = 5;
    case expired = 6;
    case extended = 7;
    case approved = 8;
    case registered = 9;
        // NGO
    case registration_incomplete = 10;

        // Project
    case pending_for_schedule = 11;
    case has_comment = 12;
    case scheduled = 13;
}
