<?php

namespace App\Enums;

enum SubPermissionEnum: int
{
        // User
    case user_add = 1;
    case user_delete = 2;
    case user_information = 3;
    case user_password = 4;
    case user_permission = 5;
    public const USERS = [
        1 => "user_add",
        2 => "user_delete",
        3 => "user_information",
        4 => "user_password",
        5 => "user_permission"
    ];
        // settings
    case setting_language = 21;
    case setting_job = 22;
    case setting_destination = 23;
    public const BASIC_SETTINGS = [
        21 => "setting_language",
    ];
    public const SETTINGS = [
        21 => "language",
        22 => "job",
        23 => "destination",
    ];
        // NGO
    case ngo_add = 51;
    case ngo_information = 52;
    case ngo_director_information = 53;
    case ngo_agreement = 54;
    case ngo_more_information = 55;
    case ngo_status = 56;
    case ngo_representative = 57;
    public const NGO = [
        51 => "add_ngo",
        52 => "account_information",
        53 => "director_information",
        54 => "agreement_checklist",
        55 => "more_information",
        56 => "status",
        57 => "representative",
    ];
        // ABOUT
    case about_director = 71;
    case about_manager = 72;
    case about_office = 73;
    case about_slider = 74;
    case about_technical = 75;
    public const ABOUT = [
        71 => "director",
        72 => "manager",
        73 => "office",
        74 => "slider",
        75 => "technical_sup"
    ];
        // NEWS
    case news_operations = 81;
    public const NEWS = [
        81 => "operations"
    ];
}
