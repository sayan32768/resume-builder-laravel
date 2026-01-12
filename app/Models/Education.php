<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'educations';

    public $timestamps = false;

    protected $appends = ['dates'];

    protected $fillable = [
        'resumeId',
        'name',
        'degree',
        'location',
        'startDate',
        'endDate',
        'grades',
        'dates'
    ];

    public function getDatesAttribute()
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }

    public function setDatesAttribute($value)
    {
        $this->startDate = $value['startDate'] ?? null;
        $this->endDate = $value['endDate'] ?? null;
    }

    protected $casts = [
        'grades' => 'array',
    ];
}
