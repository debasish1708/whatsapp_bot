<?php

namespace App\Enums;

enum SchoolAnnouncements : string
{
    case General = 'general';
    case Exam = 'exam';
    case Holiday = 'holiday';
    case Substitution = 'substitution';
    case Other = 'other';
}
