<?php

namespace App\Enums;

enum NotifierEnum: int
{
    case ngo_submitted_register_form = 1;
    case ngo_register_form_accepted = 2;
}
