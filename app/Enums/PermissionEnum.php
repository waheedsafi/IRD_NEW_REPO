<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case logs = "logs";
    case reports = "reports";
    case configurations = "configurations";
    case users = "users";
    case audit = "audit";
    case ngo = "ngo";
    case projects = "projects";
    case donor = "donor";
    case news = "management/news";
    case about = "management/about";
    case approval = "approval";
    case activity = "activity";
}
