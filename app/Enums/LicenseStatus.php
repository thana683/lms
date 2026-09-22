<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Pending = 'pending'; // self-registered (§1.3.1), awaiting branch admin approval
    case Active = 'active';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Revoked = 'revoked';
}
