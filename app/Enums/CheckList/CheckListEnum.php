<?php

namespace App\Enums\CheckList;

enum CheckListEnum: int
{
    case NgoDirectorNid = 1;
    case NgoDirectorProfile = 2;
    case director_work_permit = 3;
    case representer_document = 4;
    case ministry_of_economy_work_permit = 5;
    case articles_of_association = 6;
}
