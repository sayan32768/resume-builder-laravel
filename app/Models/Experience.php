<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $appends = ['dates'];

    protected $fillable = [
        'resumeId',
        'companyName',
        'companyAddress',
        'position',
        'startDate',
        'endDate',
        'workDescription',
        'category',
        'dates'
    ];

    protected $casts = [
        'startDate' => 'date:Y-m-d',
        'endDate'   => 'date:Y-m-d',
    ];

    public function getDatesAttribute()
    {
        return [
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
        ];
    }

    public function setDatesAttribute($value)
    {
        $this->startDate = $this->normalizeDate($value['startDate'] ?? null);
        $this->endDate   = $this->normalizeDate($value['endDate'] ?? null);
    }

    private function normalizeDate($value)
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)
            ->timezone('UTC')
            ->toDateString();
    }
}
