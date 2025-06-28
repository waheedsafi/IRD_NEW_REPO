<?php

namespace App\Enums\CheckList;

enum CheckListEnum: int
{
    case director_nid = 1;
        // case NgoDirectorProfile = 2;
    case director_work_permit = 3;
    case ngo_representor_letter = 4;
    case ministry_of_economy_work_permit = 5;
    case articles_of_association = 6;
    case ngo_register_form_en = 8;
    case ngo_register_form_ps = 9;
    case ngo_register_form_fa = 10;


        // project checklist 
    case moe_project_introduction_letter = 11;              // Project introduction letter from Ministry of Economy
    case project_articles_of_association = 12;
    case project_presentation = 13;              // Project Presentation
    case ngo_and_donor_contract = 14;         // NGO & Donor Contract Letter
    case mou_en = 15;                        // Memorandum of Understanding (English)
    case mou_fa = 16;                          // Memorandum of Understanding (Farsi)
    case mou_ps = 17;
    case project_ministry_of_economy_work_permit = 18;
}
