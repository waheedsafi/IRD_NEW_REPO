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
    case project_work_permit = 11;              // Ministry of Economic Work Permit
    case project_intro_letter = 12;              // Project introduction letter from Ministry of Economy
    case ngo_donor_contract_letter = 13;         // NGO & Donor Contract Letter
    case project_presentation = 14;              // Project Presentation
    case mou_english = 15;                        // Memorandum of Understanding (English)
    case mou_farsi = 16;                          // Memorandum of Understanding (Farsi)
    case mou_pashto = 17;
}
