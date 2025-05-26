<?php

namespace App\Enums\Type;

enum StatusTypeEnum: int
{
    case ngo_status = 1;
    case agreement_status = 2;
    case donor_status = 3;
    case project_status = 4;
    case general = 5;

    case register_form_not_completed = 1;
    case register_form_completed = 2;
    case signed_register_form_submitted = 3;
    case registered = 4;
    case blocked = 5;
    case registration_expired = 6;
    case registration_extended = 7;
    case waiting_for_project_documents = 8;
    case waiting_for_project_schedule = 9;
    case The_project_is_pending_approval = 10;
}
