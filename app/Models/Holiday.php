<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'holiday_date',
        'type',
        'holiday_year',
        'description',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'holiday_year' => 'integer',
    ];
}
