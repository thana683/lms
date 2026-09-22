<?php

namespace App\Enums;

enum ExamType: string
{
    case Objective = 'objective'; // auto-graded
    case Subjective = 'subjective'; // manually graded
}
