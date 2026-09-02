<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    const STATUS_PRESENT = 'present';

    const STATUS_ABSENT = 'absent';

    const STATUS_LATE = 'late';

    const STATUS_HALF_DAY = 'half_day';
}
