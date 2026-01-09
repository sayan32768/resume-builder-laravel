<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'resumeId',
        'name',
        'degree',
        'location',
        'startDate',
        'endDate',
        'grades',
    ];

    protected $casts = [
        'grades' => 'array',
    ];
}
