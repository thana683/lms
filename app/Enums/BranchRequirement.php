<?php

namespace App\Enums;

enum BranchRequirement: string
{
    case RequiredOwn = 'required_own'; // must belong to a branch, can only register at that branch
    case RequiredAny = 'required_any'; // must belong to a branch, can register at another branch
    case None = 'none'; // no branch required, can register anywhere
}
