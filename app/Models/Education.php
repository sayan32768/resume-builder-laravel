<?php

namespace App\Models;

use Carbon\Carbon;
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
    ];

    protected $casts = [
        'grades' => 'array',
        'startDate' => 'date:Y-m-d',
        'endDate'   => 'date:Y-m-d',
    ];


    public function getDatesAttribute()
    {
        return [
            'startDate' => $this->startDate?->format('Y-m-d'),
            'endDate'   => $this->endDate?->format('Y-m-d'),
        ];
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['startDate'] = $this->normalizeDate($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['endDate'] = $this->normalizeDate($value);
    }

    private function normalizeDate($value): ?string
    {
        if (! $value) return null;
        return Carbon::parse($value)->format('Y-m-d');
    }
}
